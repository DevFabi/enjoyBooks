<?php


namespace App\Specifications;


use App\Entity\Author;
use App\Entity\Book;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class SaveBooks implements SpecificationInterface
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

        if ($book->getAuthors() === null)
        {
            return false;
        }
        if (!key_exists("title",$bookToSave["volumeInfo"])
            || !key_exists("description",$bookToSave["volumeInfo"])
            || !key_exists("imageLinks",$bookToSave["volumeInfo"])){
            return false;
        }

        $this->createEntity($bookToSave, $book);

        return true;
    }

    private function createEntity(array $bookToSave, Book $book)
    {
        $book
            ->setVolumeId($bookToSave["id"])
            ->setTitle($bookToSave["volumeInfo"]["title"])
            ->setDescription($bookToSave["volumeInfo"]["description"])
            ->setImage($bookToSave["volumeInfo"]["imageLinks"]["thumbnail"]);

        if (key_exists("publishedDate",$bookToSave["volumeInfo"])){
            $book->setPublishedDate(new DateTime($bookToSave["volumeInfo"]["publishedDate"]));
        }

        $this->em->persist($book);
        $this->em->flush();
    }
}