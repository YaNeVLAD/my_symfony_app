<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\Basket;
use App\Service\Data\UserData;
use App\Service\Data\OrderData;
use App\Repository\UserRepositoryInterface;
use App\Repository\OrderRepositoryInterface;
use App\Repository\BasketRepositoryInterface;

class BasketService implements BasketServiceInterface
{
    //Переменные, константы и конструктор класса
    private $userRepository;

    private $orderRepository;

    private $basketRepository;

    public function __construct(
        UserRepositoryInterface $userRepository,
        OrderRepositoryInterface $orderRepository,
        BasketRepositoryInterface $basketRepository
    ) {
        $this->userRepository = $userRepository;
        $this->orderRepository = $orderRepository;
        $this->basketRepository = $basketRepository;
    }

    //Публичные методы
    public function add(?UserData $userData, ?OrderData $orderData): ?int
    {
        $basket = $this->findBasketItem($userData, $orderData);

        if ($basket) {
            $this->increaseCount($userData, $orderData);
            return null;
        } else {
            $user = $this->userRepository->findById($userData->getId());
            $order = $this->orderRepository->findById($orderData->getId());

            $basket = new Basket(
                null,
                $user,
                $order,
                1
            );

            return $this->basketRepository->store($basket);
        }
    }

    public function show(int $userId): ?array
    {
        $user = $this->userRepository->findById($userId);

        if (!$user) {
            throw new \Exception('Failed to find user with this ID');
        }

        return $this->basketRepository->findAllByUserId($userId);
    }

    public function remove(int $basketId): void
    {
        $basket = $this->basketRepository->findById($basketId);
        if (!$basket) {
            throw new \Exception('Order with this ID doesn\'t exist for this user ID');
        }

        $this->basketRepository->delete($basket);
    }

    public function removeAllByUser(int $userId): void
    {
        $basket = $this->basketRepository->findAllByUserId($userId);
        foreach ($basket as $item) {
            $this->basketRepository->delete($item);
        }
    }

    public function removeAllByOrder(int $orderId): void
    {
        $basket = $this->basketRepository->findAllByOrderId($orderId);
        foreach ($basket as $item) {
            $this->basketRepository->delete($item);
        }
    }

    public function increaseCount(?UserData $userData, ?OrderData $orderData): void
    {
        $basket = $this->findBasketItem($userData, $orderData);

        $count = $basket->getItemCount();
        $basket->setItemCount($count + 1);

        $this->basketRepository->store($basket);
    }

    public function decreaseCount(?UserData $userData, ?OrderData $orderData): void
    {
        $basket = $this->findBasketItem($userData, $orderData);

        if ($basket->getItemCount() <= 1) {
            $this->remove($basket->getId());
        } else {
            $count = $basket->getItemCount();
            $basket->setItemCount($count - 1);
            $this->basketRepository->store($basket);
        }
    }

    private function findBasketItem(?UserData $userData, ?OrderData $orderData): ?Basket
    {
        if ($userData === null || $orderData === null) {
            throw new \Exception('Failed to find user or order with this ID\'s');
        }

        $user = $this->userRepository->findById($userData->getId());
        $order = $this->orderRepository->findById($orderData->getId());

        if (!$user || !$order) {
            throw new \Exception('Failed to find order or user with received data');
        }

        return $this->basketRepository->findByUserAndOrder($user->getId(), $order->getId());
    }
}