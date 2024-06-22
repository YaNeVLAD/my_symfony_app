<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;

interface UserRepositoryInterface
{
    public function store(User $user): ?int;

    public function delete(User $user): void;

    public function findAll(): ?array;

    public function findById(int $id): ?User;

    public function findByEmail(string $email): ?User;

    public function findByPhone(?string $phone): ?User;
}