<?php

namespace App\Factory;

use App\Entity\Book;
use App\Entity\ReadBooks;
use Doctrine\ORM\EntityManagerInterface;

class ReadBookCreator extends Creator
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function create(array $bookToSave): Book
    {
        $readBook = new ReadBooks();

        $readBook->setUser($bookToSave['user'])
                 ->setBook($bookToSave['book']);

        $this->em->persist($readBook);
        $this->em->flush();

        return $bookToSave['book'];
    }
}