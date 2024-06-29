<?php
declare(strict_types=1);

namespace App\Service\Data;

class BasketData
{
    public function __construct(
        private ?int $basket_id,
        private UserData $user,
        private ProductData $product,
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

    public function getProduct(): ProductData
    {
        return $this->product;
    }
}