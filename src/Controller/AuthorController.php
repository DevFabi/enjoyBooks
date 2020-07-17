<?php

namespace App\Controller;

use App\Service\Authors\GetListOfAuthors;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AuthorController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var GetListOfAuthors
     */
    private $listOfAuthors;

    public function __construct(EntityManagerInterface $entityManager, GetListOfAuthors $listOfAuthors)
    {
        $this->entityManager = $entityManager;
        $this->listOfAuthors = $listOfAuthors;
    }

    /**
     * @Route("/author", name="author")
     */
    public function listAuthors()
    {
        $authors = $this->listOfAuthors->getAuthors();

        return $this->render('author/index.html.twig',
                                    ["authors" => $authors]);
    }
}
