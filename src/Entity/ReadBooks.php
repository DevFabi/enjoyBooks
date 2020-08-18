<?php

namespace App\Entity;

use App\Repository\ReadBooksRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=ReadBooksRepository::class)
 * @UniqueEntity(
 *     fields={"Book", "User"},
 *     message="This book already read"
 * )
 */
class ReadBooks
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Book::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $Book;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="readBooks")
     * @ORM\JoinColumn(nullable=false)
     */
    private $User;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="date")
     */
    private $createdAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBook(): ?Book
    {
        return $this->Book;
    }

    public function setBook(?Book $Book): self
    {
        $this->Book = $Book;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->User;
    }

    public function setUser(?User $User): self
    {
        $this->User = $User;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
