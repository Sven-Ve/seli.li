<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\MappedSuperclass]
abstract class _DefaultSuperclass
{
  #[ORM\Column(type: 'datetime')]
  private ?\DateTime $createdAt = null;

  #[ORM\Column(type: 'datetime', nullable: true)]
  private ?\DateTime $updatedAt = null;

  #[ORM\Column(type: 'integer')]
  private ?int $createdBy = null;

  #[ORM\Column(type: 'integer', nullable: true)]
  private ?int $updatedBy = null;

  public function getCreatedAt(): ?\DateTimeInterface
  {
    return $this->createdAt;
  }

  public function setCreatedAt(\DateTimeInterface $createdAt): self
  {
    $this->createdAt = \DateTime::createFromInterface($createdAt);

    return $this;
  }

  public function getUpdatedAt(): ?\DateTimeInterface
  {
    return $this->updatedAt;
  }

  public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
  {
    $this->updatedAt = $updatedAt;

    return $this;
  }

  public function getCreatedBy(): ?int
  {
    return $this->createdBy;
  }

  public function setCreatedBy(int $createdBy): self
  {
    $this->createdBy = $createdBy;

    return $this;
  }

  public function getUpdatedBy(): ?int
  {
    return $this->updatedBy;
  }

  public function setUpdatedBy(?int $updatedBy): self
  {
    $this->updatedBy = $updatedBy;

    return $this;
  }
}
