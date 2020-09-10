<?php


namespace App\UseCases;


use App\Entity\Book;
use App\Service\BookUploader\GoogleBookUploader;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GetAllApiBooks
{
    private $uploader;
    private $client;

    public function __construct(GoogleBookUploader $uploader, HttpClientInterface $client)
    {
        $this->uploader = $uploader;
        $this->client = $client;
    }

    public function getAllApiBooks()
    {
        $books = [];

            $url = $this->uploader->createGeneralUrl();

            $response = $this->client->request(
                'GET',
                $url
            );

            $response = json_decode($response->getContent(), true);

        return $books;
    }
}