<?php
declare(strict_types=1);

//Переделать логику: если аватар не загружен, то ничего не сохраняем
//На фронте если аватар есть - отображать его
//Если аватара нет, отображать стандартный

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageService implements ImageServiceInterface
{
    //Переменные, константы и конструктор класса
    private const ALLOWED_EXTENTIONS = ['png', 'gif', 'jpg', 'jpeg'];

    private const IMAGE_FOLDER_PATH = '\public\uploads';

    private $projectDir;

    public function __construct($projectDir)
    {
        $this->projectDir = $projectDir;
    }

    //Публичные методы
    public function save(?UploadedFile $image, int $id, ?string $folder): ?string
    {
        $ext = $this->getAndValidateExtention($image);
        if ($image && $ext) {
            $fileName = 'image' . $id . '.' . $ext;
            $folder
                ? $image->move($this->projectDir . self::IMAGE_FOLDER_PATH . '/' . $folder, $fileName)
                : $image->move($this->projectDir . self::IMAGE_FOLDER_PATH, $fileName);
            return $fileName;
        }
        return null;
    }

    public function delete(?string $imageName, ?string $folder): void
    {
        try {
            $folder
                ? unlink($this->projectDir . self::IMAGE_FOLDER_PATH . '/' . $folder . '/' . $imageName)
                : unlink($this->projectDir . self::IMAGE_FOLDER_PATH . '/' . $imageName);

        } catch (\ErrorException $e) {
            return;
        }
    }

    public function replace(UploadedFile $newImage, ?string $prevImageName, int $id, ?string $folder): string
    {
        $this->delete($prevImageName, $folder);
        $new = $this->save($newImage, $id, $folder);
        return $new;
    }

    public function getAndValidateExtention(?UploadedFile $image): mixed
    {
        if ($image) {
            $ext = $image->getClientOriginalExtension();
            return in_array($ext, self::ALLOWED_EXTENTIONS) ? $ext : false;
        } else {
            return null;
        }
    }

    public function getAllowedExtentions(): array
    {
        return self::ALLOWED_EXTENTIONS;
    }
}