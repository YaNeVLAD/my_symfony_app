<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\Product;
use App\Service\Data\ProductData;
use App\Repository\ProductRepositoryInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ProductService implements ProductServiceInterface
{
    //Переменные, константы и конструктор класса
    public const WRONG_NAME = "name";

    private const REGISTER_METHOD = 1;

    private const UPDATE_METHOD = 2;

    private const ORDER_IMAGES_DIR = 'product_images';

    private $productRepository;

    private ImageServiceInterface $imageService;

    public function __construct(ProductRepositoryInterface $productRepository, ImageServiceInterface $imageService)
    {
        $this->productRepository = $productRepository;
        $this->imageService = $imageService;
    }

    //Публичные методы
    public function find(int $productId): ProductData
    {
        $product = $this->productRepository->findById($productId);

        if ($product === null) {
            throw new \Exception('Product with ID ' . $productId . ' does\'t exist ');
        }

        $productData = $this->createFromProduct($product);

        return $productData;
    }

    public function findAllInCategory(?string $category): array
    {
        $products = $this->productRepository->findAll();

        $sorted = [];
        foreach ($products as $product) {
            if ($product->getCategorie() === $category) {
                $sorted[] = $this->createFromProduct($product);
            }
        }
        return $sorted;
    }

    public function delete(int $productId): ?string
    {
        $product = $this->productRepository->findById($productId);
        if ($product) {
            $this->imageService->delete($product->getImagePath(), self::ORDER_IMAGES_DIR);
            $this->productRepository->delete($product);
            return $product->getCategorie();
        }
        return null;
    }

    public function create(ProductData $productData, ?UploadedFile $image): int
    {
        if ($this->imageService->getAndValidateExtention($image) === false) {
            throw new \Exception("Invalid Image extention. Must be ." . implode(' .', $this->imageService->getAllowedExtentions()));
        }

        if ($field = $this->checkUniqueFields($productData, null, self::REGISTER_METHOD)) {
            throw new \Exception('This order ' . $field . ' has been already taken');
        }

        $product = $this->createFromData($productData);

        $productId = $this->productRepository->store($product);

        $imagePath = $this->imageService->save($image, $productId, self::ORDER_IMAGES_DIR);
        $product->setImagePath($imagePath);

        $this->productRepository->store($product);

        return $productId;
    }

    public function update(ProductData $productData, ?UploadedFile $avatar): int
    {
        if ($this->imageService->getAndValidateExtention($avatar) === false) {
            throw new \Exception("Invalid Image extention. Must be ." . implode(' .', $this->imageService->getAllowedExtentions()));
        }

        $productId = $productData->getId();
        $product = $this->productRepository->findById($productId);

        if ($field = $this->checkUniqueFields($productData, $product, self::UPDATE_METHOD)) {
            throw new \Exception('This order ' . $field . ' has been already taken');
        }

        $this->updateFromData($productData, $product);

        if ($avatar) {
            $prev = $this->productRepository->findById($productData->getId())->getImagePath();
            $new = $this->imageService->replace($avatar, $prev, $productId, self::ORDER_IMAGES_DIR);
            $product->setImagePath($new);
        }

        $this->productRepository->store($product);

        return $productId;
    }

    //Приватные методы
    private function createFromData(ProductData $params): Product
    {
        return new Product(
            $params->getId(),
            $params->getCategorie(),
            $params->getName(),
            $params->getDescription(),
            $params->getImagePath(),
            $params->getPrice(),
            $params->getFeatured(),
        );
    }

    private function createFromProduct(Product $product): ProductData
    {
        return new ProductData(
            $product->getId(),
            $product->getCategorie(),
            $product->getName(),
            $product->getDescription(),
            $product->getImagePath(),
            $product->getPrice(),
            $product->getFeatured(),
        );
    }


    private function updateFromData(ProductData $params, Product $product): void
    {
        $product->setCategorie($params->getCategorie());
        $product->setName($params->getName());
        $product->setDescription($params->getDescription());
        $product->setPrice($params->getPrice());
        $product->setFeatured($params->getFeatured());
    }

    private function checkUniqueFields(ProductData $productData, ?Product $product, int $method): ?string
    {
        if ($method === self::UPDATE_METHOD) {
            if (!$this->isNameUnique($product->getName(), $productData->getName())) {
                return self::WRONG_NAME;
            }
        }
        if ($method === self::REGISTER_METHOD) {
            if ($this->productRepository->findByName($productData->getName())) {
                return self::WRONG_NAME;
            }
        }
        return null;
    }

    private function isNameUnique(string $name, ?string $newName): bool
    {
        return ($name !== $newName && $this->productRepository->findByName($newName)) ? false : true;
    }
}