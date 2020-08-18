<?php

namespace App\Controller;

use App\Service\Books\GetListOfBooks;
use App\Entity\Author;
use App\Entity\Book;
use App\Form\AuthorType;
use App\Service\BookUploader\GoogleBookUploader;
use App\Specifications\CanSaveBooksSpecification;
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
    private $em;
    /**
     * @var GetListOfBooks
     */
    private $listOfBooks;

    public function __construct(EntityManagerInterface $em, GetListOfBooks $listOfBooks)
    {
        $this->em = $em;
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
    public function searchBooks(Request $request, GoogleBookUploader $bookUploader, CanSaveBooksSpecification $canSaveBooksSpecification): Response
    {
        $author = new Author();
        $books = [];
        $form = $this->createForm(AuthorType::class, $author);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            // Search book in API
           $booksFound = $bookUploader->getAllBooks([$author]);
           foreach ($booksFound as $bookToSave)
           {
               if ($canSaveBooksSpecification->isSatisfiedBy($bookToSave)) {
                   $book = new Book();
                   $book->setVolumeId($bookToSave["id"])
                       ->setTitle($bookToSave["volumeInfo"]["title"])
                       ->setImage($bookToSave["volumeInfo"]["imageLinks"]["thumbnail"])
                       ->setDescription($bookToSave["volumeInfo"]["description"]);
                   $books[] = $book;
                   }
           }
            // Search book in DB
            $author = $this->em->getRepository(Author::class)->findOneBy(['name' =>$author->getName()]);
            $db_books = $this->em->getRepository(Book::class)->findBy(['authors' => $author]);
            foreach ( $db_books as $book) {
                $books[] = $book;
            }
        }   

        return $this->render('book/search.html.twig', [
            'form' => $form->createView(),
            'books' => $books
        ]);
    }

}
