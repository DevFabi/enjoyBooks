<?php


namespace App\Service\Authors;


use App\Entity\Author;
use Doctrine\ORM\EntityManagerInterface;

class GetListOfAuthors
{

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em )
    {
        $this->em = $em;
    }

    /**
     * @return object[]
     */
    public function getAuthors()
    {
        return $this->em->getRepository(Author::class)->findAll();
    }
}