<?php


namespace App\Service\GoogleAPI;


class UrlGenerator
{
    /**
     * UrlGenerator constructor.
     * @param string $key
     */
    public function __construct(string $key) {
        $this->key = $key;
    }

    /**
     * @param $authorName
     * @return string
     */
    public function createGoogleUrl($authorName): string
    {
        // Replace space by +
        $authorName = str_replace(' ', '+', $authorName);

        $uri = "https://www.googleapis.com/books/v1/volumes";
        $param = "?q=inauthor:".$authorName;
        $otherParams = "&printType=books&projection=lite&langRestrict=fr";
        $key = "&key=".$this->key;

        return $uri.$param.$otherParams.$key;
    }


}