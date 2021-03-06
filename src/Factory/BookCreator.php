<?php

namespace App\Factory;

use App\Entity\Author;
use App\Entity\Book;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class BookCreator extends Creator
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function create(array $bookToSave): Book
    {
        $book = new Book();

        $book->setVolumeId($bookToSave['id'])
            ->setTitle($bookToSave['volumeInfo']['title'])
            ->setImage($bookToSave['volumeInfo']['imageLinks']['thumbnail']);

        if (key_exists('description', $bookToSave['volumeInfo'])) {
            $book->setDescription($bookToSave['volumeInfo']['description']);
        }

        foreach ($bookToSave['volumeInfo']['authors'] as $author) {
            $foundAuthor = $this->em->getRepository(Author::class)->findOneBy(['name' => $author]);
            if (null === $foundAuthor) {
                $foundAuthor = new Author();
                $foundAuthor->setName($author);
                $this->em->persist($foundAuthor);
                $this->em->flush();
            }
            $book->setAuthors($foundAuthor);
            continue;
        }

        if (key_exists('publishedDate', $bookToSave['volumeInfo'])) {
            $book->setPublishedDate(new DateTime($bookToSave['volumeInfo']['publishedDate']));
        }
        $this->em->persist($book);
        $this->em->flush();

        return $book;
    }
}
