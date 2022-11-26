<?php

namespace App\Entity;

use App\Repository\LinkRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[UniqueEntity(fields: ['name', 'user'], message: 'Name already exists', errorPath: 'name')]
#[ORM\UniqueConstraint(columns: ['name', 'user_id'])]
#[ORM\Entity(repositoryClass: LinkRepository::class)]
class Link extends _DefaultSuperclass implements \Stringable
{
  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  private ?int $id = null;

  #[Assert\NotBlank]
  #[Assert\Length(min: 4, max: 50, minMessage: 'Your name must be at least {{ limit }} characters long', maxMessage: 'Your name cannot be longer than {{ limit }} characters')]
  #[ORM\Column(length: 50)]
  private ?string $name = null;

  #[Assert\NotBlank]
  #[Assert\Length(min: 10, max: 255, minMessage: 'Your url must be at least {{ limit }} characters long', maxMessage: 'Your url cannot be longer than {{ limit }} characters')]
  #[ORM\Column(length: 255)]
  private ?string $url = null;

  #[ORM\ManyToOne(inversedBy: 'links')]
  #[ORM\JoinColumn(nullable: false)]
  private ?User $user = null;

  #[ORM\ManyToOne(inversedBy: 'links')]
  #[ORM\JoinColumn(nullable: false)]
  private ?Category $category = null;

  #[ORM\Column(length: 100, nullable: true)]
  private ?string $description = null;

  #[ORM\Column]
  private bool $favorite = false;

  public function getId(): ?int
  {
    return $this->id;
  }

  public function getName(): ?string
  {
    return $this->name;
  }

  public function setName(string $name): self
  {
    $this->name = $name;

    return $this;
  }

  public function getUrl(): ?string
  {
    return $this->url;
  }

  public function setUrl(string $url): self
  {
    $this->url = $url;

    return $this;
  }

  public function getUser(): ?User
  {
    return $this->user;
  }

  public function setUser(User $user): self
  {
    $this->user = $user;

    return $this;
  }

  public function getCategory(): ?Category
  {
    return $this->category;
  }

  public function setCategory(Category $category): self
  {
    $this->category = $category;

    return $this;
  }

  public function getDescription(): ?string
  {
    return $this->description;
  }

  public function setDescription(?string $description): self
  {
    $this->description = $description;

    return $this;
  }

  public function __toString()
  {
    return $this->name;
  }

  public function isFavorite(): bool
  {
    return $this->favorite;
  }

  public function setFavorite(bool $favorite): self
  {
    $this->favorite = $favorite;

    return $this;
  }
}
