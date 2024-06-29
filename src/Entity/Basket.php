<?php
namespace App\Entity;

class Basket
{
    public function __construct(
        private ?int $basket_id,
        private User $user,
        private Product $product,
        private int $itemCount,
    ) {

    }

    //GET методы
    public function getId(): int
    {
        return $this->basket_id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function getItemCount(): int
    {
        return $this->itemCount;
    }

    //SET методы
    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    public function setProduct(Product $product): void
    {
        $this->product = $product;
    }

    public function setItemCount(int $itemCount): void
    {
        $this->itemCount = $itemCount;
    }
}