<?php


namespace App\UseCases;

use App\Entity\Book;
use App\Factory\BookCreator;

class CreateBookUseCase
{
    private $bookCreator;
    public function __construct(BookCreator $bookCreator)
    {
        $this->bookCreator = $bookCreator;
    }

    public function create(array $bookToSave): Book
    {
        return $this->bookCreator->create($bookToSave);
    }

}