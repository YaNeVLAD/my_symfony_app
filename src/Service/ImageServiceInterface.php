<?php
declare(strict_types=1);

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;

interface ImageServiceInterface
{
    public function save(?UploadedFile $avatar, int $id, ?string $folder): ?string;

    public function delete(?string $imageName, ?string $folder): void;

    public function replace(UploadedFile $newImage, string $prevImageName, int $id, ?string $folder): string;

    public function getAndValidateExtention(?UploadedFile $avatar): mixed;

    public function getAllowedExtentions(): array;
}