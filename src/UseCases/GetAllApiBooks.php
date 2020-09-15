<?php


namespace App\UseCases;


use App\Entity\Author;
use App\Entity\Book;
use App\Service\BookUploader\GoogleBookUploader;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GetAllApiBooks
{

    const KEYWORD = [
        'roman',
        'océan',
        'animaux'
    ];

    private $uploader;
    private $client;

    public function __construct(GoogleBookUploader $uploader, HttpClientInterface $client)
    {
        $this->uploader = $uploader;
        $this->client = $client;
    }

    public function getAllApiBooks(): array
    {
        $books = [];
        foreach (self::KEYWORD as $keyword) {
            $response = $this->callGoogleApi($keyword);
            foreach ($response as $item) {
                $itemBook = $this->normalize($item);
                if ($itemBook != null) {
                    $books[] = $itemBook;
                }
            }
        }
        return $books;
    }

    private function callGoogleApi($keyword): array
    {
        $url = $this->uploader->createKeywordUrl($keyword);
        $response = $this->client->request(
            'GET',
            $url
        );
        $response = json_decode($response->getContent(), true);

        return $response['items'];
    }

    private function normalize($item)
    {
        $book = new Book();

        if(!key_exists("imageLinks",$item["volumeInfo"]) || !key_exists("description",$item["volumeInfo"]) || !key_exists("authors",$item["volumeInfo"])){ return null;}
        $book
            ->setVolumeId($item["id"])
            ->setTitle($item["volumeInfo"]["title"])
            ->setImage($item["volumeInfo"]["imageLinks"]["thumbnail"]);

        foreach ($item["volumeInfo"]["authors"] as $author) {
            $foundAuthor = new Author();
            $foundAuthor->setName($author);
            $book->setAuthors($foundAuthor);
            continue;
        }
        if (key_exists("publishedDate",$item["volumeInfo"])){
            $book->setPublishedDate(new \DateTime($item["volumeInfo"]["publishedDate"]));
        }

        return $book;
    }
}