<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[UniqueEntity(fields: ['name', 'user'], message: 'Name already exists', errorPath: 'name')]
#[ORM\UniqueConstraint(columns: ['name', 'user_id'])]
#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category extends _DefaultSuperclass implements \Stringable
{
  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  private ?int $id = null;

  #[Assert\NotBlank]
  #[Assert\Length(min: 4, max: 50, minMessage: 'Your name must be at least {{ limit }} characters long', maxMessage: 'Your name cannot be longer than {{ limit }} characters')]
  #[ORM\Column(length: 50)]
  private ?string $name = null;

  #[ORM\ManyToOne(inversedBy: 'categories')]
  #[ORM\JoinColumn(nullable: false)]
  private ?User $user = null;

  #[ORM\OneToMany(mappedBy: 'category', targetEntity: Link::class)]
  private Collection $links;

  #[ORM\Column]
  private bool $defaultCategory = false;

  public function __construct(User $user)
  {
    $this->user = $user;
    $this->links = new ArrayCollection();
  }

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

  public function getUser(): ?User
  {
    return $this->user;
  }

  /*
  public function setUser(User $user): self
  {
      $this->user = $user;

      return $this;
  }
  */

  /**
   * @return Collection<int, Link>
   */
  public function getLinks(): Collection
  {
    return $this->links;
  }

  public function addLink(Link $link): self
  {
    if (!$this->links->contains($link)) {
      $this->links->add($link);
      $link->setCategory($this);
    }

    return $this;
  }

  /*
    public function removeLink(Link $link): self
    {
        if ($this->links->removeElement($link)) {
            // set the owning side to null (unless already changed)
            if ($link->getCategory() === $this) {
                $link->setCategory(null);
            }
        }

        return $this;
    }
  */
  public function isDefaultCategory(): bool
  {
    return $this->defaultCategory;
  }

  public function setDefaultCategory(bool $defaultCategory): self
  {
    $this->defaultCategory = $defaultCategory;

    return $this;
  }

  public function __toString()
  {
    return $this->name;
  }
}
