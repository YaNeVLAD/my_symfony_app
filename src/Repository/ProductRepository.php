<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Product;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ProductRepositoryInterface;

class ProductRepository implements ProductRepositoryInterface
{
    //Переменные, константы и конструктор класса
    private EntityManagerInterface $em;

    private EntityRepository $repository;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->repository = $em->getRepository(Product::class);
    }

    //Публичные методы
    public function store(Product $product): ?int
    {
        $this->em->persist($product);
        $this->em->flush();

        return $product->getId();
    }

    public function delete(Product $product): void
    {
        $this->em->remove($product);
        $this->em->flush();
    }

    public function findAll(): ?array
    {
        return $this->repository->findAll();
    }

    public function findById(int $id): ?Product
    {
        return $this->em->find(Product::class, $id);
    }

    public function findByName(string $name): ?Product
    {
        return $this->repository->findOneBy(["name" => (string) $name]);
    }

    public function findByCategorie(string $category): ?Product
    {
        return $this->repository->findOneBy(["category" => (string) $category]);
    }
}