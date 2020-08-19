<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\ReadBooks;
use App\Service\BookUploader\GoogleBookUploader;
use App\UseCases\AddUserReadBookUseCase;
use App\UseCases\CreateBookUseCase;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ReadBooksController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var GoogleBookUploader
     */
    private $uploader;
    /**
     * @var HttpClientInterface
     */
    private $client;

    public function __construct(EntityManagerInterface $em, GoogleBookUploader $uploader, HttpClientInterface $client)
    {
        $this->em = $em;
        $this->uploader = $uploader;
        $this->client = $client;
    }

    /**
     * @Route("/read_books", name="readBooks")
     * @IsGranted("ROLE_USER")
     */
    public function index(): Response
    {
        $readBooks = $this->em->getRepository(ReadBooks::class)->findBy(['User' => $this->getUser()]);

        return $this->render('read_books/index.html.twig', [
            'books' => $readBooks
        ]);
    }

    /**
     * @Route("/read_book/{volumeId}", name="addReadBook")
     * @IsGranted("ROLE_USER")
     */
    public function addReadBook($volumeId, CreateBookUseCase $createBookUseCase, AddUserReadBookUseCase $addUserReadBookUseCase): Response
    {
        $book = $this->em->getRepository(Book::class)->findOneBy(['volumeId' => $volumeId]);
        if($book == null)
        {
            $url = $this->uploader->createSearchVolumeUrl($volumeId);
            $response = $this->client->request(
                'GET',
                $url
            );
            $response = json_decode($response->getContent(), true);

            $createBookUseCase->create($response['items'][0]);
            $book = $this->em->getRepository(Book::class)->findOneBy(['volumeId' => $volumeId]);
        }

        $addUserReadBookUseCase->create($this->getUser(),$book);

        $this->addFlash('success', 'Le livre '.$book->getTitle().' a bien été ajouté à votre liste.');

        $readBooks = $this->em->getRepository(ReadBooks::class)->findBy(['User' => $this->getUser()]);

       return $this->render('read_books/index.html.twig', [
           'books' => $readBooks
       ]);
    }

    /**
     * @Route("/read_book/delete/{volumeId}", name="removeReadBook")
     * @IsGranted("ROLE_USER")
     */
    public function removeReadBook($volumeId): Response
    {
        $book = $this->em->getRepository(Book::class)->findOneBy(['volumeId' => $volumeId]);

        $readBook = $this->em->getRepository(ReadBooks::class)->findOneBy(['User' => $this->getUser(), 'Book' => $book]);

        $this->em->remove($readBook);
        $this->em->flush();
        
        $this->addFlash('success', 'Le livre " '.$book->getTitle().' " a bien été supprimé de votre liste.');

        $readBooks = $this->em->getRepository(ReadBooks::class)->findBy(['User' => $this->getUser()]);

        return $this->render('read_books/index.html.twig', [
            'books' => $readBooks
        ]);
    }
}
