<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Product;

interface ProductRepositoryInterface
{
    public function store(Product $product): ?int;

    public function delete(Product $product): void;

    public function findAll(): ?array;

    public function findById(int $id): ?Product;

    public function findByName(string $name): ?Product;

    public function findByCategorie(string $category): ?Product;
}