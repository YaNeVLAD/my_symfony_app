<?php
declare(strict_types=1);

namespace App\Entity;

class Product
{
    public function __construct(
        private ?int $productId,
        private string $category,
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
        return $this->category;
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

    //SET методы
    public function setCategorie(string $category): void
    {
        $this->category = $category;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function setImagePath(?string $imagePath): void
    {
        $this->imagePath = $imagePath;
    }

    public function setPrice(int $price): void
    {
        $this->price = $price;
    }

	public function setFeatured(int $featured): void {
		$this->featured = $featured;
	}
}