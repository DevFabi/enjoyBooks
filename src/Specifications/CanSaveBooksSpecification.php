<?php


namespace App\Specifications;


use App\Entity\Author;
use App\Entity\Book;
use Doctrine\ORM\EntityManagerInterface;

class CanSaveBooksSpecification implements SpecificationInterface
{
    private $em;

    public function __construct(EntityManagerInterface $em) {
        $this->em = $em;
    }

    public function isSatisfiedBy($bookToSave): bool
    {
        $book = new Book();
        if (key_exists("authors",$bookToSave["volumeInfo"])){
            foreach ($bookToSave["volumeInfo"]["authors"] as $author) {
                $foundAuthor = $this->em->getRepository(Author::class)->findOneBy(["name" => $author]);
                if ($foundAuthor !== null) {
                    $book->setAuthors($foundAuthor);
                    continue;
                }
            }
        }

        if (!key_exists("title",$bookToSave["volumeInfo"])
            || !key_exists("description",$bookToSave["volumeInfo"])
            || !key_exists("imageLinks",$bookToSave["volumeInfo"])){
            return false;
        }

        return true;
    }

}