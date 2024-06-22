<?php
declare(strict_types=1);

namespace App\Entity;

class ListObject
{
    public function __construct(
        private ?int $userId,
        private string $firstName,
        private string $lastName,
        private string $email,
        private ?string $avatarPath,
    ) {

    }

    //GET методы
    public function getId(): ?int
    {
        return $this->userId;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getAvatarPath(): ?string
    {
        return $this->avatarPath;
    }

}