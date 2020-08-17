<?php

namespace App\Controller;

use App\Service\Books\GetListOfBooks;
use App\Entity\Author;
use App\Form\AuthorType;
use App\Service\BookUploader\GoogleBookUploader;
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

    /**
     * @Route("/searchBooks", name="searchBooks")
     */
    public function searchBooks(Request $request, GoogleBookUploader $bookUploader): Response
    {
        $author = new Author();
        $books = [];
        $form = $this->createForm(AuthorType::class, $author);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
           $books = $bookUploader->getAllBooks([$author]);
        }   

        return $this->render('book/search.html.twig', [
            'form' => $form->createView(),
            'books' => $books
        ]);
    }

}
