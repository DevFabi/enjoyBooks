<?php


namespace App\Service\BookUploader;


interface BookUploaderInterface
{
    public function getAllBooks(Array $authors);
    public function createUrl($authorName): string;
}