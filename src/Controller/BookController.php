<?php

namespace App\Controller;

use App\Service\Books\GetListOfBooks;
use App\Entity\Author;
use App\Entity\Book;
use App\Form\AuthorType;
use App\Service\BookUploader\GoogleBookUploader;
use App\Specifications\CanSaveBooksSpecification;
use Doctrine\ORM\EntityManagerInterface;
use Elastica\Client;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Elastica\Query;
use Elastica\Query\BoolQuery;
use Elastica\Query\Match;

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
     * @IsGranted("ROLE_USER")
     */
    public function searchBooks(Request $request, GoogleBookUploader $bookUploader, CanSaveBooksSpecification $canSaveBooksSpecification, Client $client): Response
    {
        $author = new Author();
        $books = [];
        $form = $this->createForm(AuthorType::class, $author);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
          
            $match = new Match();
            $match->setField('author', $author->getName());

            $bool = new BoolQuery();
            $bool->addShould($match);

            $elasticaQuery = new Query($bool);

            $foundBooks = $client->getIndex('book')->search($elasticaQuery);
            $books = [];

            foreach ($foundBooks as $book) {
                $books[] = $book->getSource();
            }
        }   

        return $this->render('book/search.html.twig', [
            'form' => $form->createView(),
            'books' => $books
        ]);
    }

}
