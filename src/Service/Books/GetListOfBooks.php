<?php

declare(strict_types=1);

namespace App\Service\Books;

use App\Entity\Author;
use App\Entity\Book;
use Doctrine\ORM\EntityManagerInterface;

class GetListOfBooks
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @return object[]
     */
    public function getAllBooks()
    {
        return $this->em->getRepository(Book::class)->findBy([], ['publishedDate' => 'desc']);
    }

    /**
     * @param $authorId
     *
     * @return object[]
     */
    public function getBookByAuthors($authorId)
    {
        $author = $this->em->getRepository(Author::class)->findOneBy(['id' => $authorId]);

        return $this->em->getRepository(Book::class)->findBy(['authors' => $author], ['publishedDate' => 'desc']);
    }

    /**
     * @param $date
     *
     * @return mixed
     */
    public function getLastBooks($date)
    {
        return $this->em->getRepository(Book::class)->findByAddedDate($date);
    }
}
