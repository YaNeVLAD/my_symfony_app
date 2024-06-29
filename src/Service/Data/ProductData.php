<?php
declare(strict_types=1);

namespace App\Service\Data;

class ProductData
{
    public function __construct(
        private ?int $productId,
        private string $categorie,
        private string $name,
        private ?string $description,
        private ?string $imagePath,
        private int $price,
        private int $featured,
    ) {

    }

    //GET методы
    public function getId(): ?int
    {
        return $this->productId;
    }

    public function getCategorie(): string
    {
        return $this->categorie;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getImagePath(): ?string
    {
        return $this->imagePath;
    }

    public function getPrice(): int
    {
        return $this->price;
    }
    
	public function getFeatured(): int {
		return $this->featured;
	}
}