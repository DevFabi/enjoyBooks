<?php
declare(strict_types= 1);

namespace App\Service\BookUploader;


use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

abstract class AbstractBookUploader implements BookUploaderInterface
{
    protected $key;
    protected $url;
    /**
     * @var HttpClientInterface
     */
    protected $client;
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    public function __construct(string $url, string $key, HttpClientInterface $client, EntityManagerInterface $em)
    {
        $this->url = $url;
        $this->key = $key;
        $this->client = $client;
        $this->em = $em;
    }

    public function createUrl($authorName): string
    {
        $param = '%s%s';
        $author = sprintf($param, '?q=inauthor:', str_replace(' ', '+', $authorName));

        $data = [
            'printType' => 'books',
            'projection' => 'lite',
            'langRestrict' => 'fr',
            'key' => $this->key
        ];

        $url = '%s%s%s%s';
;
        return sprintf($url, $this->url, $author,'&', http_build_query($data));
    }
}