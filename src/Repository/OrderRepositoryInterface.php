<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Order;

interface OrderRepositoryInterface
{
    public function store(Order $order): ?int;

    public function delete(Order $order): void;

    public function findAll(): ?array;

    public function findById(int $id): ?Order;

    public function findByName(string $name): ?Order;

    public function findByCategorie(string $category): ?Order;
}