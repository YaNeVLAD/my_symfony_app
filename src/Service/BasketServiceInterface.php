<?php
declare(strict_types=1);

namespace App\Service;

use App\Service\Data\UserData;
use App\Service\Data\OrderData;

interface BasketServiceInterface
{
    public function add(UserData $userData, OrderData $orderData): ?int;

    public function show(int $userId): ?array;

    public function remove(int $basketId): void;

    public function removeAllByUser(int $userId): void;

    public function removeAllByOrder(int $orderId): void;
    
    public function increaseCount(UserData $userData, OrderData $orderData): void;

    public function decreaseCount(UserData $userData, OrderData $orderData): void;
}