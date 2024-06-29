<?php
declare(strict_types=1);

namespace App\Service;

use App\Service\Data\ProductData;
use Symfony\Component\HttpFoundation\File\UploadedFile;

interface ProductServiceInterface
{
    public function find(int $productId): ProductData;

    public function findAllInCategory(?string $category): array;

    public function delete(int $productId): ?string;

    public function create(ProductData $productData, ?UploadedFile $image): int;

    public function update(ProductData $productData, ?UploadedFile $avatar): int;
}