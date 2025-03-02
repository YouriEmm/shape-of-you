<?php
namespace App\Entity;

use App\Repository\ClothingItemRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ClothingItemRepository::class)]
class ClothingItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    /**
     * @var Collection<int, Outfit>
     */
    #[ORM\ManyToMany(targetEntity: Outfit::class, mappedBy: 'items')]
    private Collection $outfits;

    /**
     * @var Collection<int, Wardrobe>
     */
    #[ORM\ManyToMany(targetEntity: Wardrobe::class, mappedBy: 'items')]
    private Collection $wardrobes;

    #[ORM\ManyToOne(inversedBy: 'clothingItems')]
    private ?Partner $brand = null;

    #[ORM\Column(length: 255)]
    private ?string $category = null;

    public function __construct()
    {
        $this->outfits = new ArrayCollection();
        $this->wardrobes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }


    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;
        return $this;
    }

    /**
     * @return Collection<int, Outfit>
     */
    public function getOutfits(): Collection
    {
        return $this->outfits;
    }

    public function addOutfit(Outfit $outfit): static
    {
        if (!$this->outfits->contains($outfit)) {
            $this->outfits->add($outfit);
            $outfit->addItem($this);
        }
        return $this;
    }

    public function removeOutfit(Outfit $outfit): static
    {
        if ($this->outfits->removeElement($outfit)) {
            $outfit->removeItem($this);
        }
        return $this;
    }

    /**
     * @return Collection<int, Wardrobe>
     */
    public function getWardrobes(): Collection
     {
        return $this->wardrobes;
    }

    public function addWardrobe(Wardrobe $wardrobe): static
    {
        if (!$this->wardrobes->contains($wardrobe)) {
            $this->wardrobes->add($wardrobe);
            $wardrobe->addItem($this);
        }

        return $this;
    }

    public function removeWardrobe(Wardrobe $wardrobe): static
    {
        if ($this->wardrobes->removeElement($wardrobe)) {
            $wardrobe->removeItem($this);
        }

        return $this;
    }

    public function getBrand(): ?Partner
    {
        return $this->brand;
    }

    public function setBrand(?Partner $brand): static
    {
        $this->brand = $brand;

        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(string $category): static
    {
        $this->category = $category;

        return $this;
    }
}
