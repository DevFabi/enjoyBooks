<?php


namespace App\Factory;

use App\Entity\Book;

abstract class Creator 
{
    abstract public function create(array $objectToCreate): Book;
}