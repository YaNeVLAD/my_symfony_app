<?php
namespace App\Entity;

class Basket
{
    public function __construct(
        private ?int $basket_id,
        private User $user,
        private Order $order,
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

    public function getOrder(): Order
    {
        return $this->order;
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

    public function setOrder(Order $order): void
    {
        $this->order = $order;
    }

    public function setItemCount(int $itemCount): void
    {
        $this->itemCount = $itemCount;
    }
}