<?php

declare(strict_types = 1);

namespace App\Service\Books;


use App\Entity\Author;
use App\Entity\Book;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class SaveBook
{
    private $em;

    /**
     * SaveBook constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em) {
        $this->em = $em;
    }

    /**
     * @param array $books
     * @return int
     * @throws Exception
     */
    public function save(array $books): int
    {
        $booksSaved = 0;

        foreach ($books as $bookToSave) {

            $book = new Book();
            if (key_exists("authors",$bookToSave["volumeInfo"])){
                foreach ($bookToSave["volumeInfo"]["authors"] as $author) {

                    $foundAuthor = $this->em->getRepository(Author::class)->findOneBy(['name' => $author]);

                    if ($foundAuthor !== null) {
                        $book->setAuthors($foundAuthor);
                        continue;
                    }
                }
            } else {
                continue;
            }

            if ($book->getAuthors() === null){
                continue;
            }
            if (!key_exists('title',$bookToSave["volumeInfo"])
                || !key_exists('description',$bookToSave["volumeInfo"])
                || !key_exists('imageLinks',$bookToSave["volumeInfo"])){
                continue;
            }

            $book
                ->setVolumeId($bookToSave["id"])
                ->setTitle($bookToSave["volumeInfo"]["title"])
                ->setDescription($bookToSave["volumeInfo"]["description"])
                ->setImage($bookToSave["volumeInfo"]["imageLinks"]["thumbnail"]);


            if (key_exists('publishedDate',$bookToSave["volumeInfo"])){
                $book->setPublishedDate(new DateTime($bookToSave["volumeInfo"]["publishedDate"]));
            }

            $this->em->persist($book);
            $booksSaved++;
        }

        $this->em->flush();

        return $booksSaved;

    }
}