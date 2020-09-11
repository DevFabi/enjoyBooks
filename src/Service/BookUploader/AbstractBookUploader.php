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
        $data = [
            'printType' => 'books',
            'projection' => 'lite',
            'langRestrict' => 'fr',
            'key' => $this->key
        ];

        return sprintf('%s?q=inauthor:%s&%s', $this->url, str_replace(' ', '+', $authorName), http_build_query($data));
    }

    public function createSearchVolumeUrl($volumeId): string
    {
        $data = [
            'projection' => 'lite',
            'langRestrict' => 'fr',
            'key' => $this->key
        ];

        return sprintf('%s?q=%s&%s', $this->url, $volumeId, http_build_query($data));
    }

    public function createKeywordUrl($keyword): string
    {
        $data = [
            'printType' => 'books',
            'projection' => 'lite',
            'langRestrict' => 'fr',
            'maxResults' => 40,
            'orderBy' => 'newest',
            'key' => $this->key,
        ];

        return sprintf('%s?q=%s&%s', $this->url, $keyword, http_build_query($data));
    }
}