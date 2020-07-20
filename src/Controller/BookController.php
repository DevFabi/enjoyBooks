<?php

namespace App\Controller;

use App\Service\Books\GetListOfBooks;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var GetListOfBooks
     */
    private $listOfBooks;

    public function __construct(EntityManagerInterface $entityManager, GetListOfBooks $listOfBooks)
    {
        $this->entityManager = $entityManager;
        $this->listOfBooks = $listOfBooks;
    }

    /**
     * @Route("/books", name="books")
     */
    public function listAllBooks(Request $request, PaginatorInterface $paginator): Response
    {
        $data = $this->listOfBooks->getAllBooks();

        $books = $paginator->paginate(
            $data,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('book/index.html.twig', [
            'books' => $books,
        ]);
    }

    /**
     * @Route("/book/{authorId}", name="bookByAuthor")
     */
    public function listBooksByAuthor($authorId, Request $request, PaginatorInterface $paginator): Response
    {
        $data = $this->listOfBooks->getBookByAuthors($authorId);
        
        $books = $paginator->paginate(
            $data,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('book/index.html.twig', [
            'books' => $books
        ]);
    }
}
