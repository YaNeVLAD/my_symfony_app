<?php
declare(strict_types=1);

namespace App\Service;

use App\Service\Data\OrderData;
use Symfony\Component\HttpFoundation\File\UploadedFile;

interface OrderServiceInterface
{
    public function find(int $orderId): OrderData;

    public function findAllInCategory(?string $category): array;

    public function delete(int $orderId): ?string;

    public function create(OrderData $orderData, ?UploadedFile $image): int;

    public function update(OrderData $orderData, ?UploadedFile $avatar): int;
}