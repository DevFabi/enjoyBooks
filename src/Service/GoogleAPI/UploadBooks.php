<?php


namespace App\Service\GoogleAPI;


use App\Entity\Book;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class UploadBooks
{
    private $client;
    private $em;
    private $urlGenerator;

    /**
     * UploadBooks constructor.
     * @param UrlGenerator $urlGenerator
     * @param HttpClientInterface $client
     * @param EntityManagerInterface $em
     */
    public function __construct(UrlGenerator $urlGenerator, HttpClientInterface $client, EntityManagerInterface $em)
    {
        $this->client = $client;
        $this->em = $em;
        $this->urlGenerator = $urlGenerator;
    }

    public function getAllBooks(Array $authors) {

        $books = [];

        foreach ($authors as $author){
            $url = $this->urlGenerator->createGoogleUrl($author->getName());

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