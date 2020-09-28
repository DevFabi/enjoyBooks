<?php

namespace App\Entity;

use App\Repository\BookRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=BookRepository::class)
 * @ORM\HasLifecycleCallbacks
 * @UniqueEntity(
 *     fields={"volumeId"},
 *     message="This volume already exist"
 * )
 */
class Book
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $publishedDate;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Url()
     */
    private $image;

    /**
     * @ORM\ManyToOne(targetEntity=Author::class, inversedBy="books")
     */
    private $authors;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $volumeId;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $added;

    public function getId()
    {
        return $this->id;
    }

    public function getPublishedDate(): ?DateTimeInterface
    {
        return $this->publishedDate;
    }

    public function setPublishedDate(DateTimeInterface $publishedDate): self
    {
        $this->publishedDate = $publishedDate;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getAuthors(): ?Author
    {
        return $this->authors;
    }

    public function setAuthors(?Author $authors): self
    {
        $this->authors = $authors;

        return $this;
    }

    public function getVolumeId(): ?string
    {
        return $this->volumeId;
    }

    public function setVolumeId(?string $volumeId): self
    {
        $this->volumeId = $volumeId;

        return $this;
    }

    /**
     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->added = new DateTime('now');
    }
}
