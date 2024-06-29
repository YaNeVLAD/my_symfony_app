<?php
declare(strict_types=1);

namespace App\Service;

use App\Service\Data\UserData;
use App\Service\Data\ProductData;

interface BasketServiceInterface
{
    public function add(UserData $userData, ProductData $productData): ?int;

    public function show(int $userId): ?array;

    public function remove(int $basketId): void;

    public function removeAllByUser(int $userId): void;

    public function removeAllByProduct(int $productId): void;
    
    public function increaseCount(UserData $userData, ProductData $productData): void;

    public function decreaseCount(UserData $userData, ProductData $productData): void;
}