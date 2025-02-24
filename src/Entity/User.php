<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity('email')]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Assert\Email]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $role = null;

    /**
     * @var Collection<int, Outfit>
     */
    #[ORM\OneToMany(targetEntity: Outfit::class, mappedBy: 'owner')]
    private Collection $outfits;

    #[ORM\OneToOne(mappedBy: 'owner', cascade: ['persist', 'remove'])]
    private ?Wardrobe $wardrobe = null;

    /**
     * @var Collection<int, HistoryEntry>
     */
    #[ORM\OneToMany(targetEntity: HistoryEntry::class, mappedBy: 'owner')]
    private Collection $historyEntries;

    /**
     * @var Collection<int, Publication>
     */
    #[ORM\OneToMany(targetEntity: Publication::class, mappedBy: 'owner', orphanRemoval: true)]
    private Collection $publications;

    /**
     * @var Collection<int, Comment>
     */
    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'owner', orphanRemoval: true)]
    private Collection $comments;

    /**
     * @var Collection<int, Like>
     */
    #[ORM\OneToMany(targetEntity: Like::class, mappedBy: 'owner')]
    private Collection $likes;

    public function __construct()
    {
        $this->outfits = new ArrayCollection();
        $this->historyEntries = new ArrayCollection();
        $this->publications = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->likes = new ArrayCollection();
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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): static
    {
        $this->role = $role;

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
            $outfit->setOwner($this);
        }

        return $this;
    }

    public function removeOutfit(Outfit $outfit): static
    {
        if ($this->outfits->removeElement($outfit)) {
            // set the owning side to null (unless already changed)
            if ($outfit->getOwner() === $this) {
                $outfit->setOwner(null);
            }
        }

        return $this;
    }

    public function getWardrobe(): ?Wardrobe
    {
        return $this->wardrobe;
    }

    public function setWardrobe(Wardrobe $wardrobe): static
    {
        // set the owning side of the relation if necessary
        if ($wardrobe->getOwner() !== $this) {
            $wardrobe->setOwner($this);
        }

        $this->wardrobe = $wardrobe;

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
            $historyEntry->setOwner($this);
        }

        return $this;
    }

    public function removeHistoryEntry(HistoryEntry $historyEntry): static
    {
        if ($this->historyEntries->removeElement($historyEntry)) {
            // set the owning side to null (unless already changed)
            if ($historyEntry->getOwner() === $this) {
                $historyEntry->setOwner(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Publication>
     */
    public function getPublications(): Collection
    {
        return $this->publications;
    }

    public function addPublication(Publication $publication): static
    {
        if (!$this->publications->contains($publication)) {
            $this->publications->add($publication);
            $publication->setOwner($this);
        }

        return $this;
    }

    public function removePublication(Publication $publication): static
    {
        if ($this->publications->removeElement($publication)) {
            // set the owning side to null (unless already changed)
            if ($publication->getOwner() === $this) {
                $publication->setOwner(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setOwner($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getOwner() === $this) {
                $comment->setOwner(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Like>
     */
    public function getLikes(): Collection
    {
        return $this->likes;
    }

    public function addLike(Like $like): static
    {
        if (!$this->likes->contains($like)) {
            $this->likes->add($like);
            $like->setOwner($this);
        }

        return $this;
    }

    public function removeLike(Like $like): static
    {
        if ($this->likes->removeElement($like)) {
            // set the owning side to null (unless already changed)
            if ($like->getOwner() === $this) {
                $like->setOwner(null);
            }
        }

        return $this;
    }
}
