<?php

namespace App\Entity;

use App\Repository\OutfitRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OutfitRepository::class)]
class Outfit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'outfits')]
    private ?User $owner = null;

    /**
     * @var Collection<int, ClothingItem>
     */
    #[ORM\ManyToMany(targetEntity: ClothingItem::class, inversedBy: 'outfits')]
    private Collection $items;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    /**
     * @var Collection<int, HistoryEntry>
     */
    #[ORM\OneToMany(targetEntity: HistoryEntry::class, mappedBy: 'outfit')]
    private Collection $historyEntries;

    /**
     * @var Collection<int, Like>
     */
    #[ORM\OneToMany(targetEntity: Like::class, mappedBy: 'outfit', cascade: ['remove'])]
    private Collection $likes;

    #[ORM\Column]
    private ?bool $public = null;


    /**
     * @var Collection<int, Comment>
     */
    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'outfit', cascade: ['remove'])]
    private Collection $comments;

    public function __construct()
    {
        $this->items = new ArrayCollection();
        $this->historyEntries = new ArrayCollection();
        $this->likes = new ArrayCollection();
        $this->comments = new ArrayCollection();

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

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): static
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * @return Collection<int, ClothingItem>
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItem(ClothingItem $item): static
    {
        if (!$this->items->contains($item)) {
            $this->items->add($item);
        }

        return $this;
    }

    public function removeItem(ClothingItem $item): static
    {
        $this->items->removeElement($item);

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return Collection<int, HistoryEntry>
     */
    public function getHistoryEntries(): Collection
    {
        return $this->historyEntries;
    }

    public function addHistoryEntry(HistoryEntry $historyEntry): static
    {
        if (!$this->historyEntries->contains($historyEntry)) {
            $this->historyEntries->add($historyEntry);
            $historyEntry->setOutfit($this);
        }

        return $this;
    }

    public function removeHistoryEntry(HistoryEntry $historyEntry): static
    {
        if ($this->historyEntries->removeElement($historyEntry)) {
            // set the owning side to null (unless already changed)
            if ($historyEntry->getOutfit() === $this) {
                $historyEntry->setOutfit(null);
            }
        }

        return $this;
    }

    public function getLikes(): Collection
    {
        return $this->likes;
    }
    
    public function addLike(Like $like): static
    {
        if (!$this->likes->contains($like)) {
            $this->likes->add($like);
            $like->setOutfit($this);
        }
    
        return $this;
    }
    
    public function removeLike(Like $like): static
    {
        if ($this->likes->removeElement($like)) {
            if ($like->getOutfit() === $this) {
                $like->setOutfit(null);
            }
        }
    
        return $this;
    }

    public function getLikesCount(): int
    {
        return $this->likes->count();
    }

    public function isPublic(): ?bool
    {
        return $this->public;
    }

    public function setPublic(bool $public): static
    {
        $this->public = $public;

        return $this;
    }

    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setOutfit($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            if ($comment->getOutfit() === $this) {
                $comment->setOutfit(null);
            }
        }

        return $this;
    }
}
