<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use App\Entity\ListObject;
use App\Service\Data\UserData;
use App\Repository\UserRepositoryInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UserService implements UserServiceInterface
{
    //Переменные, константы и конструктор класса
    public const WRONG_EMAIL = "email";

    public const WRONG_PHONE = "phone";

    private const REGISTER_METHOD = 1;

    private const UPDATE_METHOD = 2;

    private const OK_STATUS = 'ok';

    private const USER_IMAGES_DIR = 'user_images';

    private UserRepositoryInterface $userRepository;

    private ImageServiceInterface $imageService;

    public function __construct(UserRepositoryInterface $userRepository, ImageServiceInterface $imageService)
    {
        $this->userRepository = $userRepository;
        $this->imageService = $imageService;
    }

    //Публичные методы
    public function getUser(int $userId): UserData
    {
        $user = $this->userRepository->findById($userId);

        if ($user === null) {
            throw new \Exception('User with ID ' . $userId . ' does\'t exist ');
        }

        $userData = $this->createFromUser($user);

        return $userData;
    }

    public function getUserByEmail(string $email): UserData
    {
        return $this->createFromUser($this->userRepository->findByEmail($email));
    }

    public function getAllUsers(): ?array
    {
        $users = $this->userRepository->findAll();
        $list = [];
        foreach ($users as $user) {
            $list[] = new ListObject(
                $user->getId(),
                $user->getFirstName(),
                $user->getLastName(),
                $user->getEmail(),
                $user->getAvatarPath()
            );
        }
        return $list;
    }

    public function deleteUser(int $userId): void
    {
        $user = $this->userRepository->findById($userId);
        $this->imageService->delete($user->getAvatarPath(), self::USER_IMAGES_DIR);
        $this->userRepository->delete($user);
    }

    public function createUser(UserData $userData, ?UploadedFile $avatar): int
    {
        if ($this->imageService->getAndValidateExtention($avatar) === false) {
            throw new \Exception("Invalid Image extention. Must be ." . implode(' .', $this->imageService->getAllowedExtentions()));
        }

        if ($field = $this->checkUniqueFields($userData, null, self::REGISTER_METHOD)) {
            throw new \Exception('Your ' . $field . ' has been already taken');
        }

        $user = $this->createFromData($userData);

        $userId = $this->userRepository->store($user);

        $avatarPath = $this->imageService->save($avatar, $userId, self::USER_IMAGES_DIR);
        $user->setAvatarPath($avatarPath);

        $this->userRepository->store($user);

        return $userId;
    }

    public function editUser(UserData $userData, ?UploadedFile $avatar): int
    {
        if ($this->imageService->getAndValidateExtention($avatar) === false) {
            throw new \Exception("Invalid Image extention. Must be ." . implode(' .', $this->imageService->getAllowedExtentions()));
        }

        $userId = $userData->getId();
        $user = $this->userRepository->findById($userId);

        if ($field = $this->checkUniqueFields($userData, $user, self::UPDATE_METHOD)) {
            throw new \Exception('Your ' . $field . ' has been already taken');
        }

        $this->updateFromData($userData, $user);

        if ($avatar) {
            $prev = $this->userRepository->findById($userData->getId())->getAvatarPath();
            $new = $this->imageService->replace($avatar, $prev, $userId, self::USER_IMAGES_DIR);
            $user->setAvatarPath($new);
        }

        $this->userRepository->store($user);

        return $userId;
    }

    //Приватные методы
    private function createFromData(UserData $userData): User
    {
        return new User(
            $userData->getId(),
            $userData->getFirstName(),
            $userData->getLastName(),
            $userData->getMiddleName(),
            $userData->getGender(),
            $userData->getBirthDate(),
            $userData->getEmail(),
            $userData->getPhone(),
            $userData->getAvatarPath(),
        );
    }

    private function createFromUser(User $user): UserData
    {
        return new UserData(
            $user->getId(),
            $user->getFirstName(),
            $user->getLastName(),
            $user->getMiddleName(),
            $user->getGender(),
            $user->getBirthDate(),
            $user->getEmail(),
            $user->getPhone(),
            $user->getAvatarPath(),
        );
    }

    private function updateFromData(UserData $userData, User $user): void
    {
        $user->setFirstName($userData->getFirstName());
        $user->setLastName($userData->getLastName());
        $user->setMiddleName($userData->getMiddleName());
        $user->setGender($userData->getGender());
        $user->setBirthDate($userData->getBirthDate());
        $user->setEmail($userData->getEmail());
        $user->setPhone($userData->getPhone());
    }

    private function checkUniqueFields(UserData $userData, ?User $user, int $method): ?string
    {
        if ($method === self::UPDATE_METHOD) {
            if (!$this->isEmailUnique($user->getEmail(), $userData->getEmail())) {
                return self::WRONG_EMAIL;
            }
            if (!$this->isPhoneUnique($user->getPhone(), $userData->getPhone())) {
                return self::WRONG_PHONE;
            }
        }
        if ($method === self::REGISTER_METHOD) {
            if ($this->userRepository->findByEmail($userData->getEmail())) {
                return self::WRONG_EMAIL;
            }
            if ($this->userRepository->findByPhone($userData->getPhone())) {
                return self::WRONG_PHONE;
            }
        }
        return null;
    }

    private function isEmailUnique(string $email, ?string $newEmail): bool
    {
        return ($email !== $newEmail && $this->userRepository->findByEmail($newEmail)) ? false : true;
    }

    private function isPhoneUnique(?string $phone, ?string $newPhone): bool
    {
        return ($phone !== $newPhone && $this->userRepository->findByPhone($newPhone)) ? false : true;
    }
}