<?php


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
        // Replace space by +
        $authorName = str_replace(' ', '+', $authorName);

        $param = "?q=inauthor:".$authorName;
        $otherParams = "&printType=books&projection=lite&langRestrict=fr";
        $key = "&key=".$this->key;

        return $this->url.$param.$otherParams.$key;
    }
}