<?php

// src/Entity/Wish.php

namespace App\Entity;

use App\Repository\WishRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: WishRepository::class)]
class Wish
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Please provide an idea!')]
    #[Assert\Length(
        min: 5,
        max: 255,
        minMessage: 'Minimum {{ limit }} characters please!',
        maxMessage: 'Maximum {{ limit }} characters please!'
    )]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Assert\Length(
        min: 5,
        max: 5000,
        minMessage: "Minimum {{ limit }} characters please!",
        maxMessage: "Maximum {{ limit }} characters please!"
    )]
    private ?string $description = null;

    #[ORM\Column]
    private ?bool $isPublished = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateCreated = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateUpdated = null;

    #[ORM\ManyToOne(inversedBy: 'wishes')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Category $category = null;

    #[ORM\ManyToOne(inversedBy: 'wishes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    /**
     * @var Collection<int, Comment>
     */
    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'wish')]
    private Collection $comments;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
    }

    // --- Getters/Setters ---

    public function getId(): ?int { return $this->id; }

    public function getTitle(): ?string { return $this->title; }

    public function setTitle(string $title): static {
        $this->title = $title;
        return $this;
    }

    public function getDescription(): ?string { return $this->description; }

    public function setDescription(?string $description): static {
        $this->description = $description;
        return $this;
    }

    public function isPublished(): ?bool { return $this->isPublished; }

    public function setIsPublished(bool $isPublished): static {
        $this->isPublished = $isPublished;
        return $this;
    }

    public function getDateCreated(): ?\DateTimeInterface { return $this->dateCreated; }

    public function setDateCreated(\DateTimeInterface $dateCreated): static {
        $this->dateCreated = $dateCreated;
        return $this;
    }

    public function getDateUpdated(): ?\DateTimeInterface { return $this->dateUpdated; }

    public function setDateUpdated(?\DateTimeInterface $dateUpdated): static {
        $this->dateUpdated = $dateUpdated;
        return $this;
    }

    public function getCategory(): ?Category { return $this->category; }

    public function setCategory(?Category $category): static {
        $this->category = $category;
        return $this;
    }

    public function getUser(): ?User { return $this->user; }

    public function setUser(?User $user): static {
        $this->user = $user;
        return $this;
    }

    public function __toString(): string
    {
        return $this->title;
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
            $comment->setWish($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getWish() === $this) {
                $comment->setWish(null);
            }
        }

        return $this;
    }
}
