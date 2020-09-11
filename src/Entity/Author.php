<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @UniqueEntity(
 *     groups={"create"},
 *     fields={"name"},
 *     message="This author already exist"
 * )
 */
class Author
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(
     *      min = 5,
     *      max = 155,
     *      minMessage = "Name must be at least {{ limit }} characters long",
     *      maxMessage = "Name cannot be longer than {{ limit }} characters",
     *      allowEmptyString = false,
     *      groups={"create"}
     * )
     * @Assert\Length(
     *      min = 4,
     *      max = 155,
     *      minMessage = "Name must be at least {{ limit }} characters long",
     *      maxMessage = "Name cannot be longer than {{ limit }} characters",
     *      allowEmptyString = false,
     *      groups={"search"}
     * )
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity=Book::class, mappedBy="authors")
     */
    private $books;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, mappedBy="subscriptions")
     */
    private $users;

    public function __construct()
    {
        $this->books = new ArrayCollection();
        $this->users = new ArrayCollection();
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

    /**
     * @return Collection|Book[]
     */
    public function getBooks(): Collection
    {
        return $this->books;
    }

    public function addBook(Book $book): self
    {
        if (!$this->books->contains($book)) {
            $this->books[] = $book;
            $book->setAuthors($this);
        }

        return $this;
    }

    public function removeBook(Book $book): self
    {
        if ($this->books->contains($book)) {
            $this->books->removeElement($book);
            // set the owning side to null (unless already changed)
            if ($book->getAuthors() === $this) {
                $book->setAuthors(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->addSubscription($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->contains($user)) {
            $this->users->removeElement($user);
            $user->removeSubscription($this);
        }

        return $this;
    }

    public function __toString()
    {
        return $this->getName();
    }

}
