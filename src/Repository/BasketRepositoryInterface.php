<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Basket;

interface BasketRepositoryInterface
{
    public function store(Basket $basket): ?int;

    public function delete(Basket $basket): void;

    public function findAll(): ?array;

    public function findById(int $id): ?Basket;

    public function findAllByUserId(int $id): ?array;

    public function findAllByOrderId(int $id): ?array;

    public function findByUserAndOrder(int $userId, int $orderId): ?Basket;
}