<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Order;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\OrderRepositoryInterface;

class OrderRepository implements OrderRepositoryInterface
{
    //Переменные, константы и конструктор класса
    private EntityManagerInterface $em;

    private EntityRepository $repository;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->repository = $em->getRepository(Order::class);
    }

    //Публичные методы
    public function store(Order $order): ?int
    {
        $this->em->persist($order);
        $this->em->flush();

        return $order->getId();
    }

    public function delete(Order $order): void
    {
        $this->em->remove($order);
        $this->em->flush();
    }

    public function findAll(): ?array
    {
        return $this->repository->findAll();
    }

    public function findById(int $id): ?Order
    {
        return $this->em->find(Order::class, $id);
    }

    public function findByName(string $name): ?Order
    {
        return $this->repository->findOneBy(["name" => (string) $name]);
    }

    public function findByCategorie(string $category): ?Order
    {
        return $this->repository->findOneBy(["categorie" => (string) $category]);
    }
}