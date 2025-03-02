<?php

namespace App\Entity;

use App\Repository\DetectedClothingRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DetectedClothingRepository::class)]
class DetectedClothing
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $imagePath = null;

    #[ORM\Column]
    private array $detectedItems = [];

    #[ORM\ManyToOne(inversedBy: 'detectedClothing')]
    private ?User $owner = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getImagePath(): ?string
    {
        return $this->imagePath;
    }

    public function setImagePath(string $imagePath): static
    {
        $this->imagePath = $imagePath;

        return $this;
    }

    public function getDetectedItems(): array
    {
        return $this->detectedItems;
    }

    public function setDetectedItems(array $detectedItems): static
    {
        $this->detectedItems = $detectedItems;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): static
    {
        $this->owner = $owner;

        return $this;
    }
}
