<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{
    //Переменные, константы и конструктор класса
    private EntityManagerInterface $em;

    private EntityRepository $repository;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->repository = $em->getRepository(User::class);
    }

    //Публичные методы
    public function store(User $user): ?int
    {
        $this->em->persist($user);
        $this->em->flush();

        return $user->getId();
    }

    public function delete(User $user): void
    {
        $this->em->remove($user);
        $this->em->flush();
    }

    public function findAll(): ?array
    {
        return $this->repository->findAll();
    }

    public function findById(int $id): ?User
    {
        return $this->em->find(User::class, $id);
    }

    public function findByEmail(string $email): ?User
    {
        return $this->repository->findOneBy(["email" => (string) $email]);
    }

    public function findByPhone(?string $phone): ?User
    {
        return $this->repository->findOneBy(["phone" => (string) $phone]);
    }
}