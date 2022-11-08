<?php

namespace App\Entity;

use App\Repository\LinkRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[UniqueEntity(fields: ['name', 'user'], message: 'Name already exists', errorPath: 'name')]
#[ORM\UniqueConstraint(columns: ['name', 'user_id'])]
#[ORM\Entity(repositoryClass: LinkRepository::class)]
class Link
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
  #[ORM\Column(length: 255)]
  private ?string $url = null;

  #[ORM\ManyToOne(inversedBy: 'links')]
  #[ORM\JoinColumn(nullable: false)]
  private ?User $user = null;

  #[ORM\ManyToOne(inversedBy: 'links')]
  #[ORM\JoinColumn(nullable: false)]
  private ?Category $category = null;

  #[ORM\Column]
  private bool $isFavorite = false;

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

  public function isFavorite(): bool
  {
    return $this->isFavorite;
  }

  public function setFavorite(bool $isFavorite): self
  {
    $this->isFavorite = $isFavorite;

    return $this;
  }
}
