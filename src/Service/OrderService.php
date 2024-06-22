<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\Order;
use App\Repository\OrderRepositoryInterface;
use App\Service\Data\OrderData;
use App\Repository\OrderRepository;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class OrderService implements OrderServiceInterface
{
    //Переменные, константы и конструктор класса
    public const WRONG_NAME = "name";

    private const REGISTER_METHOD = 1;

    private const UPDATE_METHOD = 2;

    private const ORDER_IMAGES_DIR = 'order_images';

    private $orderRepository;

    private ImageServiceInterface $imageService;

    public function __construct(OrderRepositoryInterface $orderRepository, ImageServiceInterface $imageService)
    {
        $this->orderRepository = $orderRepository;
        $this->imageService = $imageService;
    }

    //Публичные методы
    public function find(int $orderId): OrderData
    {
        $order = $this->orderRepository->findById($orderId);

        if ($order === null) {
            throw new \Exception('Order with ID ' . $orderId . ' does\'t exist ');
        }

        $orderData = $this->createFromOrder($order);

        return $orderData;
    }

    public function findAllInCategory(?string $category): array
    {
        $orders = $this->orderRepository->findAll();

        $sorted = [];
        foreach ($orders as $order) {
            if ($order->getCategorie() === $category) {
                $sorted[] = $this->createFromOrder($order);
            }
        }
        return $sorted;
    }

    public function delete(int $orderId): ?string
    {
        $order = $this->orderRepository->findById($orderId);
        if ($order) {
            $this->imageService->delete($order->getImagePath(), self::ORDER_IMAGES_DIR);
            $this->orderRepository->delete($order);
            return $order->getCategorie();
        }
        return null;
    }

    public function create(OrderData $orderData, ?UploadedFile $image): int
    {
        if ($this->imageService->getAndValidateExtention($image) === false) {
            throw new \Exception("Invalid Image extention. Must be ." . implode(' .', $this->imageService->getAllowedExtentions()));
        }

        if ($field = $this->checkUniqueFields($orderData, null, self::REGISTER_METHOD)) {
            throw new \Exception('This order ' . $field . ' has been already taken');
        }

        $order = $this->createFromData($orderData);

        $orderId = $this->orderRepository->store($order);

        $imagePath = $this->imageService->save($image, $orderId, self::ORDER_IMAGES_DIR);
        $order->setImagePath($imagePath);

        $this->orderRepository->store($order);

        return $orderId;
    }

    public function update(OrderData $orderData, ?UploadedFile $avatar): int
    {
        if ($this->imageService->getAndValidateExtention($avatar) === false) {
            throw new \Exception("Invalid Image extention. Must be ." . implode(' .', $this->imageService->getAllowedExtentions()));
        }

        $orderId = $orderData->getId();
        $order = $this->orderRepository->findById($orderId);

        if ($field = $this->checkUniqueFields($orderData, $order, self::UPDATE_METHOD)) {
            throw new \Exception('This order ' . $field . ' has been already taken');
        }

        $this->updateFromData($orderData, $order);

        if ($avatar) {
            $prev = $this->orderRepository->findById($orderData->getId())->getImagePath();
            $new = $this->imageService->replace($avatar, $prev, $orderId, self::ORDER_IMAGES_DIR);
            $order->setImagePath($new);
        }

        $this->orderRepository->store($order);

        return $orderId;
    }

    //Приватные методы
    private function createFromData(OrderData $params): Order
    {
        return new Order(
            $params->getId(),
            $params->getCategorie(),
            $params->getName(),
            $params->getDescription(),
            $params->getImagePath(),
            $params->getPrice(),
            $params->getFeatured(),
        );
    }

    private function createFromOrder(Order $order): OrderData
    {
        return new OrderData(
            $order->getId(),
            $order->getCategorie(),
            $order->getName(),
            $order->getDescription(),
            $order->getImagePath(),
            $order->getPrice(),
            $order->getFeatured(),
        );
    }


    private function updateFromData(OrderData $params, Order $order): void
    {
        $order->setCategorie($params->getCategorie());
        $order->setName($params->getName());
        $order->setDescription($params->getDescription());
        $order->setPrice($params->getPrice());
        $order->setFeatured($params->getFeatured());
    }

    private function checkUniqueFields(OrderData $orderData, ?Order $order, int $method): ?string
    {
        if ($method === self::UPDATE_METHOD) {
            if (!$this->isNameUnique($order->getName(), $orderData->getName())) {
                return self::WRONG_NAME;
            }
        }
        if ($method === self::REGISTER_METHOD) {
            if ($this->orderRepository->findByName($orderData->getName())) {
                return self::WRONG_NAME;
            }
        }
        return null;
    }

    private function isNameUnique(string $name, ?string $newName): bool
    {
        return ($name !== $newName && $this->orderRepository->findByName($newName)) ? false : true;
    }
}