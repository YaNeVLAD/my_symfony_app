<?php
declare(strict_types=1);

namespace App\Service\Data;

class BasketData
{
    public function __construct(
        private ?int $basket_id,
        private UserData $user,
        private OrderData $order,
    ) {

    }

    //GET методы
    public function getId(): int
    {
        return $this->basket_id;
    }

    public function getUser(): UserData
    {
        return $this->user;
    }

    public function getOrder(): OrderData
    {
        return $this->order;
    }
}