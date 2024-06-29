<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\Basket;
use App\Service\Data\UserData;
use App\Service\Data\ProductData;
use App\Repository\UserRepositoryInterface;
use App\Repository\ProductRepositoryInterface;
use App\Repository\BasketRepositoryInterface;

class BasketService implements BasketServiceInterface
{
    //Переменные, константы и конструктор класса
    private $userRepository;

    private $productRepository;

    private $basketRepository;

    public function __construct(
        UserRepositoryInterface $userRepository,
        ProductRepositoryInterface $productRepository,
        BasketRepositoryInterface $basketRepository
    ) {
        $this->userRepository = $userRepository;
        $this->productRepository = $productRepository;
        $this->basketRepository = $basketRepository;
    }

    //Публичные методы
    public function add(?UserData $userData, ?ProductData $productData): ?int
    {
        $basket = $this->findBasketItem($userData, $productData);

        if ($basket) {
            $this->increaseCount($userData, $productData);
            return null;
        } else {
            $user = $this->userRepository->findById($userData->getId());
            $product = $this->productRepository->findById($productData->getId());

            $basket = new Basket(
                null,
                $user,
                $product,
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
            throw new \Exception('Product with this ID doesn\'t exist for this user ID');
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

    public function removeAllByProduct(int $productId): void
    {
        $basket = $this->basketRepository->findAllByProductId($productId);
        foreach ($basket as $item) {
            $this->basketRepository->delete($item);
        }
    }

    public function increaseCount(?UserData $userData, ?ProductData $productData): void
    {
        $basket = $this->findBasketItem($userData, $productData);

        $count = $basket->getItemCount();
        $basket->setItemCount($count + 1);

        $this->basketRepository->store($basket);
    }

    public function decreaseCount(?UserData $userData, ?ProductData $productData): void
    {
        $basket = $this->findBasketItem($userData, $productData);

        if ($basket->getItemCount() <= 1) {
            $this->remove($basket->getId());
        } else {
            $count = $basket->getItemCount();
            $basket->setItemCount($count - 1);
            $this->basketRepository->store($basket);
        }
    }

    private function findBasketItem(?UserData $userData, ?ProductData $productData): ?Basket
    {
        if ($userData === null || $productData === null) {
            throw new \Exception('Failed to find user or order with this ID\'s');
        }

        $user = $this->userRepository->findById($userData->getId());
        $product = $this->productRepository->findById($productData->getId());

        if (!$user || !$product) {
            throw new \Exception('Failed to find order or user with received data');
        }

        return $this->basketRepository->findByUserAndProduct($user->getId(), $product->getId());
    }
}