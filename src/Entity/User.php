<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity('email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
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

    #[ORM\Column(type: 'json')] 
    private array $roles = [];

    #[ORM\OneToMany(targetEntity: Outfit::class, mappedBy: 'owner', cascade: ['remove'])]
    private Collection $outfits;

    #[ORM\OneToOne(mappedBy: 'owner', cascade: ['persist', 'remove'])]
    private ?Wardrobe $wardrobe = null;

    /**
     * @var Collection<int, HistoryEntry>
     */
    #[ORM\OneToMany(targetEntity: HistoryEntry::class, mappedBy: 'owner')]
    private Collection $historyEntries;

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

    /**
     * @var Collection<int, DetectedClothing>
     */
    #[ORM\OneToMany(targetEntity: DetectedClothing::class, mappedBy: 'owner', cascade: ['remove'])]
    private Collection $detectedClothing;

    public function __construct()
    {
        $this->outfits = new ArrayCollection();
        $this->historyEntries = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->likes = new ArrayCollection();
        $this->detectedClothing = new ArrayCollection();
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

    public function getRole(): ?array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

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
            if ($historyEntry->getOwner() === $this) {
                $historyEntry->setOwner(null);
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
            if ($like->getOwner() === $this) {
                $like->setOwner(null);
            }
        }

        return $this;
    }

    
    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

        /**
     * The public representation of the user (e.g. a username, an email address, etc.)
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @return Collection<int, DetectedClothing>
     */
    public function getDetectedClothing(): Collection
    {
        return $this->detectedClothing;
    }

    public function addDetectedClothing(DetectedClothing $detectedClothing): static
    {
        if (!$this->detectedClothing->contains($detectedClothing)) {
            $this->detectedClothing->add($detectedClothing);
            $detectedClothing->setOwner($this);
        }

        return $this;
    }

    public function removeDetectedClothing(DetectedClothing $detectedClothing): static
    {
        if ($this->detectedClothing->removeElement($detectedClothing)) {
            // set the owning side to null (unless already changed)
            if ($detectedClothing->getOwner() === $this) {
                $detectedClothing->setOwner(null);
            }
        }

        return $this;
    }
}
