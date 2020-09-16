<?php


namespace App\UseCases;


use App\Entity\ReadBooks;
use App\Factory\ReadBookCreator;
use Doctrine\ORM\EntityManagerInterface;

class AddUserReadBookUseCase
{
    private $em;
    private $readBookCreator;

    public function __construct(EntityManagerInterface $em, ReadBookCreator $readBookCreator)
    {
        $this->em = $em;
        $this->readBookCreator = $readBookCreator;
    }

    public function create($user, $book)
    {
        $readBook= [];

        $ifAlreadyExist = $this->em->getRepository(ReadBooks::class)->findOneBy(['User' => $user, 'Book' => $readBook]);
        
        if ($ifAlreadyExist === null) {
            $readBook['user'] = $user;
            $readBook['book'] = $book;
            $this->readBookCreator->create($readBook);
        }
    }
}