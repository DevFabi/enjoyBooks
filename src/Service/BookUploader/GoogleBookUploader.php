<?php

declare(strict_types = 1);

namespace App\Service\BookUploader;


use App\Entity\Book;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GoogleBookUploader extends AbstractBookUploader
{
    public function __construct(string $url, string $key, HttpClientInterface $client, EntityManagerInterface $em)
    {
        parent::__construct($url, $key, $client, $em);
    }

    public function getAllBooks(Array $authors) : array
    {

        $books = [];

        foreach ($authors as $author){
            $url = $this->createUrl($author->getName());

            $response = $this->client->request(
                'GET',
                $url
            );

            $response = json_decode($response->getContent(), true);

            foreach ($response["items"] as $book) {
                // Search if id exist il database
                $ifBookExist = $this->em->getRepository(Book::class)->findOneBy(['volumeId' => $book["id"]]);
                if ($ifBookExist === null){
                    $books[] = $book;
                }
            }
        }

        return $books;
    }
}