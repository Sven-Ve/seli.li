<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Scheb\TwoFactorBundle\Model\BackupCodeInterface;
use Scheb\TwoFactorBundle\Model\Totp\TwoFactorInterface;
use Scheb\TwoFactorBundle\Model\TrustedDeviceInterface;
use Svc\TotpBundle\Service\_TotpTrait;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface, TwoFactorInterface, TrustedDeviceInterface, BackupCodeInterface
{
  use _TotpTrait;

  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  private ?int $id = null;

  #[ORM\Column(length: 180, unique: true)]
  private ?string $email = null;

  #[ORM\Column]
  private array $roles = [];

  /**
   * @var string The hashed password
   */
  #[ORM\Column]
  private ?string $password = null;

  #[ORM\Column]
  private bool $isVerified = false;

  #[ORM\Column(length: 100)]
  private ?string $firstName = null;

  #[ORM\Column(length: 100)]
  private ?string $lastName = null;

  #[ORM\Column(length: 3)]
  private ?string $country = null;

  #[ORM\Column(options: ['default' => false])]
  private bool $isBlocked = false;

  #[ORM\Column(length: 100, nullable: true)]
  private ?string $blockReason = null;

  #[ORM\OneToMany(mappedBy: 'user', targetEntity: Category::class, orphanRemoval: true)]
  private Collection $categories;

  #[ORM\OneToMany(mappedBy: 'user', targetEntity: Link::class, orphanRemoval: true)]
  private Collection $links;

  public function __construct()
  {
    $this->categories = new ArrayCollection();
    $this->links = new ArrayCollection();
  }

  public function getId(): ?int
  {
    return $this->id;
  }

  public function getEmail(): ?string
  {
    return $this->email;
  }

  public function setEmail(string $email): self
  {
    $this->email = $email;

    return $this;
  }

  /**
   * A visual identifier that represents this user.
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

  public function setRoles(array $roles): self
  {
    $this->roles = $roles;

    return $this;
  }

  /**
   * @see PasswordAuthenticatedUserInterface
   */
  public function getPassword(): string
  {
    return $this->password;
  }

  public function setPassword(string $password): self
  {
    $this->password = $password;

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

  public function isVerified(): bool
  {
    return $this->isVerified;
  }

  public function setIsVerified(bool $isVerified): self
  {
    $this->isVerified = $isVerified;

    return $this;
  }

  public function getFirstName(): ?string
  {
    return $this->firstName;
  }

  public function setFirstName(string $firstName): self
  {
    $this->firstName = $firstName;

    return $this;
  }

  public function getLastName(): ?string
  {
    return $this->lastName;
  }

  public function setLastName(string $lastName): self
  {
    $this->lastName = $lastName;

    return $this;
  }

  public function getCountry(): ?string
  {
    return $this->country;
  }

  public function setCountry(string $country): self
  {
    $this->country = $country;

    return $this;
  }

  public function isBlocked(): ?bool
  {
    return $this->isBlocked;
  }

  public function setIsBlocked(bool $isBlocked): self
  {
    $this->isBlocked = $isBlocked;

    return $this;
  }

  public function getBlockReason(): ?string
  {
    return $this->blockReason;
  }

  public function setBlockReason(?string $blockReason): self
  {
    $this->blockReason = $blockReason;

    return $this;
  }

  /**
   * @return Collection<int, Category>
   */
  public function getCategories(): Collection
  {
    return $this->categories;
  }

  public function addCategory(Category $category): self
  {
    if (!$this->categories->contains($category)) {
      $this->categories->add($category);
  //        $category->setUser($this);
    }

    return $this;
  }

  /*
  public function removeCategory(Category $category): self
  {
      if ($this->categories->removeElement($category)) {
          // set the owning side to null (unless already changed)
          if ($category->getUser() === $this) {
              $category->setUser(null);
          }
      }

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
      $link->setUser($this);
    }

    return $this;
  }

  /*
  public function removeLink(Link $link): self
  {
      if ($this->links->removeElement($link)) {
          // set the owning side to null (unless already changed)
          if ($link->getUser() === $this) {
              $link->setUser(null);
          }
      }

      return $this;
  }
  */
}
