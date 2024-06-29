<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\UserRole;
use App\Service\Data\UserData;
use App\Service\UserServiceInterface;
use App\Service\BasketServiceInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{
    //Переменные, константы и конструктор класса
    private const NOT_NULLABLE_FORM_FIELDS = ['first_name', 'last_name', 'gender', 'birth_date', 'email', 'password'];

    private $userService;

    private $basketService;

    public function __construct(UserServiceInterface $userService, BasketServiceInterface $basketService)
    {
        $this->userService = $userService;
        $this->basketService = $basketService;
    }

    //Публичные методы
    public function showRegisterForm(Request $request): Response
    {
        $email = $request->getSession()->get(Security::LAST_USERNAME, '');

        if ($this->userService->authorize(UserServiceInterface::AUTHORIZATION, $email, null)) {
            $user = $this->userService->getUserByEmail($email);

            return $this->redirectToRoute('show_user', ['userId' => $user->getId()]);
        }

        return $this->render('user/register/register_page.html.twig');
    }

    public function errorPage(Request $request): Response
    {
        return $this->render('custom_error/error_page.html.twig', [
            'errorText' => $request->get('errorText'),
            'errorTitle' => $request->get('errorTitle'),
        ]);
    }

    public function viewUser(Request $request, int $userId): Response
    {
        try {
            $email = $request->getSession()->get(Security::LAST_USERNAME, '');

            if ($this->userService->authorize(UserServiceInterface::COMPARE, $email, $userId)) {
                $requestedUser = $this->userService->getUser($userId);

                return $this->render('user/view/view_page.html.twig', ['user' => $requestedUser]);
            } else {
                throw new \Exception('Access Denied');
            }
        } catch (\Exception $e) {
            return $this->redirectToRoute('error_user', [
                'errorTitle' => 'Failed To Show User',
                'errorText' => $e->getMessage(),
            ]);
        }
    }

    public function viewAllUsers(Request $request): Response
    {
        try {
            $email = $request->getSession()->get(Security::LAST_USERNAME, '');

            if ($this->userService->authorize(UserServiceInterface::ROLE, $email, null)) {
                $list = $this->userService->getAllUsers();

                return $this->render('user/list/list_page.html.twig', ['users_list' => $list]);
            } else {
                throw new \Exception('Access Denied');
            }
        } catch (\Exception $e) {
            return $this->redirectToRoute('error_user', [
                'errorTitle' => 'Failed To Show Users List',
                'errorText' => $e->getMessage(),
            ]);
        }
    }

    public function showUpdateForm(Request $request, int $userId): Response
    {
        try {
            $email = $request->getSession()->get(Security::LAST_USERNAME, '');

            if ($this->userService->authorize(UserServiceInterface::COMPARE, $email, $userId)) {
                $requestedUser = $this->userService->getUser($userId);

                return $this->render('user/update/update_page.html.twig', ['user' => $requestedUser]);;
            } else {
                throw new \Exception('Access Denied');
            }
        } catch (\Exception $e) {
            return $this->redirectToRoute('error_user', [
                'errorTitle' => 'Failed To Show Update Form',
                'errorText' => $e->getMessage(),
            ]);
        }
    }

    public function removeUser(Request $request, int $userId): Response
    {
        try {
            $email = $request->getSession()->get(Security::LAST_USERNAME, '');

            if ($this->userService->authorize(UserServiceInterface::COMPARE, $email, $userId)) {
                $this->basketService->removeAllByUser($userId);
                $this->userService->deleteUser($userId);

                return $this->redirectToRoute('index');
            } else {
                throw new \Exception('Access Denied');
            }
        } catch (\Exception $e) {
            return $this->redirectToRoute('error_user', [
                'errorTitle' => 'Failed To Delete User',
                'errorText' => $e->getMessage(),
            ]);
        }
    }

    public function registerUser(Request $request): Response
    {
        try {
            $avatar = $request->files->get('avatar_path');

            $userData = $this->createFromRequest($request);

            $userId = $this->userService->createUser($userData, $avatar);
        } catch (\Exception $e) {
            return $this->redirectToRoute('error_user', [
                'errorTitle' => 'Failed To Add User',
                'errorText' => $e->getMessage(),
            ]);
        }

        return $this->redirectToRoute('login_user');
    }

    public function updateUser(Request $request, int $userId): Response
    {
        try {
            $newAvatar = $request->files->get('avatar_path');

            $userData = $this->createFromRequest($request);

            $userId = $this->userService->editUser($userData, $newAvatar);
        } catch (\Exception $e) {
            return $this->redirectToRoute('error_user', [
                'errorTitle' => 'Failed To Update User',
                'errorText' => $e->getMessage(),
            ]);
        }

        return $this->redirectToRoute('show_user', ['userId' => $userId]);
    }

    //Приватные методы
    private function createFromRequest(Request $request): UserData
    {
        return new UserData(
            (int) $request->get('userId') ?? null,
            $this->validateData($request, 'first_name'),
            $this->validateData($request, 'last_name'),
            $this->validateData($request, 'middle_name'),
            $this->validateData($request, 'gender'),
            $this->createDate($request, 'birth_date'),
            $this->validateData($request, 'email'),
            $this->validateData($request, 'phone'),
            null,
            $this->validateData($request, 'password'),
            UserRole::USER
        );
    }

    private function validateData(Request $request, string $formFieldName): ?string
    {
        $formFieldValue = $request->get($formFieldName);
        if (in_array($formFieldName, self::NOT_NULLABLE_FORM_FIELDS) and $formFieldValue === '') {
            throw new \Exception('Missing User`s ' . $formFieldName);
        } elseif ($formFieldValue === '') {
            return null;
        }

        return $formFieldValue;
    }

    private function createDate(Request $request, string $fieldName): \DateTimeImmutable
    {
        $date = \DateTimeImmutable::createFromFormat('Y-m-d', $request->get($fieldName));
        if ($date === false) {
            throw new \Exception('Invalid Date format. Must be Y-m-d');
        } else {
            return $date;
        }
    }
}