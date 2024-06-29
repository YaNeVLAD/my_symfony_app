<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Basket;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\BasketRepositoryInterface;

class BasketRepository implements BasketRepositoryInterface
{
    //Переменные, константы и конструктор класса
    private EntityManagerInterface $em;

    private EntityRepository $repository;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->repository = $em->getRepository(Basket::class);
    }

    //Публичные методы
    public function store(Basket $basket): ?int
    {
        $this->em->persist($basket);
        $this->em->flush();

        return $basket->getId();
    }

    public function delete(Basket $basket): void
    {
        $this->em->remove($basket);
        $this->em->flush();
    }

    public function findAll(): ?array
    {
        return $this->repository->findAll();
    }

    public function findById(int $id): ?Basket
    {
        return $this->em->find(Basket::class, $id);
    }

    public function findAllByUserId(int $id): ?array
    {
        return $this->repository->findBy(["user" => $id]);
    }

    public function findAllByProductId(int $id): ?array
    {
        return $this->repository->findBy(["product" => $id]);
    }

    public function findByUserAndProduct(int $userId, int $productId): ?Basket
    {
        return $this->repository->findOneBy(["product" => $productId, "user" => $userId]);
    }
}