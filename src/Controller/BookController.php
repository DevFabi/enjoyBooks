<?php

namespace App\Controller;

use App\Entity\Author;
use App\Form\AuthorType;
use App\Service\Books\GetListOfBooks;
use Doctrine\ORM\EntityManagerInterface;
use Elastica\Client;
use Elastica\Query;
use Elastica\Query\BoolQuery;
use Elastica\Query\Match;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
            'books' => $books,
        ]);
    }

    /**
     * @Route("/searchBooks", name="searchBooks")
     * @IsGranted("ROLE_USER")
     */
    public function searchBooks(Request $request, Client $client): Response
    {
        $author = new Author();
        $books = [];
        $form = $this->createForm(AuthorType::class, $author);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $match = new Match();
            $match->setFieldQuery('author', $author->getName());

            $match->setFieldFuzziness('author', '2')
                ->setFieldMinimumShouldMatch('author', '100%');

            $bool = new BoolQuery();
            $bool->addMust($match);

            $elasticaQuery = new Query($bool);
            $foundBooks = $client->getIndex('book')->search($elasticaQuery);

            $books = [];

            foreach ($foundBooks as $book) {
                $books[] = $book->getSource();
            }
        }

        return $this->render('book/search.html.twig', [
            'form' => $form->createView(),
            'books' => $books,
        ]);
    }

    /**
     * @Route("/searchBooksBis", name="searchBooksBis")
     * @IsGranted("ROLE_USER")
     */
    public function searchBooksBis(Request $request, Client $client): Response
    {
        $books = [];
        $form = $this->createFormBuilder()->add('text', TextType::class)->getForm();
        $form->handleRequest($request);

        $result = $form->getData();
        if ($form->isSubmitted() && $form->isValid()) {
            $match = new Query\MultiMatch();
            $match->setFields(['title', 'author'])->setQuery($result['text']);

            $match->setFuzziness('2');
            $match->setMinimumShouldMatch('100%');

            $bool = new BoolQuery();
            $bool->addShould($match);

            $elasticaQuery = new Query($bool);

            $foundBooks = $client->getIndex('book')->search($elasticaQuery);
            $books = [];

            foreach ($foundBooks as $book) {
                $books[] = $book->getSource();
            }
        }

        return $this->render('book/searchBis.html.twig', [
            'form' => $form->createView(),
            'books' => $books,
        ]);
    }
}
