<?php

namespace App\Elasticsearch;

use App\Entity\Book;
use App\Repository\BookRepository;
use App\UseCases\GetAllApiBooks;
use Elastica\Client;
use Elastica\Document;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class BookIndexer
{
    private $client;
    private $bookRepository;
    private $router;
    private $allApiBooks;

    public function __construct(Client $client, BookRepository $bookRepository, UrlGeneratorInterface $router, GetAllApiBooks $allApiBooks)
    {
        $this->client = $client;
        $this->bookRepository = $bookRepository;
        $this->router = $router;
        $this->allApiBooks = $allApiBooks;
    }

    public function buildDocument(Book $book)
    {
        return new Document(
            $book->getId(),
            [
                'volumeId' => $book->getVolumeId(),
                'title' => $book->getTitle(),
                'description' => $book->getDescription(),
                'author' => $book->getAuthors()->getName(),
                'image' => $book->getImage(),
                'publishedDate' => $book->getPublishedDate(),
            ],
            'book' // Types are deprecated, to be removed in Elastic 7
        );
    }

    public function indexAllDocuments($indexName)
    {
        // DATABASE
        $allBooks = $this->bookRepository->findAll();

        // API
        $allApiBooks = $this->allApiBooks->getAllApiBooks();

        $books = array_merge($allApiBooks, $allBooks);

        $index = $this->client->getIndex($indexName);

        $documents = [];
        foreach ($books as $book) {
            $documents[] = $this->buildDocument($book);
        }

        $index->addDocuments($documents);
        $index->refresh();
    }
}
