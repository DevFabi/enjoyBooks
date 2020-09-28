<?php

namespace App\Service\BookUploader;

interface BookUploaderInterface
{
    public function getAllBooks(array $authors);

    public function createUrl($authorName): string;
}
