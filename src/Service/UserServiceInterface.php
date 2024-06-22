<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use App\Service\Data\UserData;
use Symfony\Component\HttpFoundation\File\UploadedFile;

interface UserServiceInterface
{
    public function createUser(UserData $userData, ?UploadedFile $avatar): int;

    public function getUser(int $userId): UserData;

    public function getUserByEmail(string $email): UserData;

    public function getAllUsers(): ?array;

    public function editUser(UserData $userData, ?UploadedFile $avatar): int;

    public function deleteUser(int $userId): void;
}