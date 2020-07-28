<?php

declare(strict_types = 1);

namespace App\Service\Notification;


use App\Entity\Book;
use App\Message\SendNotification;
use App\Service\Books\GetListOfBooks;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpStamp;
use Symfony\Component\Messenger\MessageBusInterface;

class NotifiedUser implements NotificationInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var GetListOfBooks
     */
    private $listOfBooks;
    /**
     * @var MessageBusInterface
     */
    private $bus;

    public function __construct(EntityManagerInterface $em, GetListOfBooks $listOfBooks, MessageBusInterface $bus)
    {
        $this->em = $em;
        $this->listOfBooks = $listOfBooks;
        $this->bus = $bus;
    }

    public function personToNotify(DateTime $date): int
    {
        $books = $this->listOfBooks->getLastBooks($date);

        $emailSend = 0;

        /** @var Book $book */
        foreach ($books as $book) {

            $author = $book->getAuthors();
            $users = $author->getUsers();

            $message = "Author :".$author->getName()." publish a new book :".$book->getTitle();
            dump('Book title : '.$book->getTitle());
            foreach ($users as $user){

                $this->dispatchNotification($message, $user->getEmail());
                $emailSend++;
            }
        }

        return $emailSend;
    }


    public function dispatchNotification($message, $email)
    {
        $this->bus->dispatch(new SendNotification($message, $email), [new AmqpStamp('email')]);
    }
}