<?php


namespace App\UseCases;


use App\Entity\ReadBooks;
use Doctrine\ORM\EntityManagerInterface;

class AddUserReadBookUseCase
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function create($user, $book)
    {
        $ifAlreadyExist = $this->em->getRepository(ReadBooks::class)->findOneBy(['User' => $user, 'Book' => $book]);
        if ($ifAlreadyExist === null) {
            $readBook = new ReadBooks();
            $readBook->setUser($user)->setBook($book);
            $this->em->persist($readBook);
            $this->em->flush();
        }
    }
}