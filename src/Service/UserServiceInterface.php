<?php
declare(strict_types=1);

namespace App\Service;

use App\Service\Data\UserData;
use Symfony\Component\HttpFoundation\File\UploadedFile;

interface UserServiceInterface
{
    public const COMPARE = 1;

    public const AUTHORIZATION = 2;

    public const ROLE = 3;

    public function createUser(UserData $userData, ?UploadedFile $avatar): int;

    public function getUser(int $userId): UserData;

    public function getUserByEmail(string $email): UserData;

    public function getAllUsers(): ?array;

    public function editUser(UserData $userData, ?UploadedFile $avatar): int;

    public function deleteUser(int $userId): void;

    public function authorize(int $method, ?string $email, ?int $userId): ?bool;
}