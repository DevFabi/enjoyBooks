<?php
declare(strict_types = 1);

namespace App\Command;


use App\Entity\Author;
use App\Events\Events;
use App\Service\Books\SaveBook;
use App\Service\BookUploader\BookUploaderInterface;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;


class UpdateBooksCommand extends Command
{
    protected static $defaultName = 'app:update-books';

    private $em;
    private $saveBook;
    /**
     * @var BookUploaderInterface
     */
    private $bookUploader;
    /**
     * @var EventDispatcher
     */
    private $eventDispatcher;

    public function __construct(BookUploaderInterface $bookUploader, EntityManagerInterface $em, SaveBook $saveBook,EventDispatcherInterface $eventDispatcher)
    {
        $this->bookUploader = $bookUploader;
        $this->em = $em;
        $this->saveBook = $saveBook;
        parent::__construct();
        $this->eventDispatcher = $eventDispatcher;
    }

    protected function configure()
    {
        $this
            ->setDescription('Update the list of books')
            ->setHelp('This command allows you update the list of books')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $date = new DateTime('now');
        $output->writeln(date_format($date, 'd/m/Y H:i:s').' : command launch');

        // 1/ Get all authors
        $authors = $this->em->getRepository(Author::class)->findAll();

        // 2/ Get all books from googleAPI which are not in database
        $books = $this->bookUploader->getAllBooks($authors);

        $output->writeln(count($books).' books (not saved)');

        // 3/ Save books in database
        $savedBooks = $this->saveBook->save($books);

        $output->writeln($savedBooks.' books saved!');

        // 4/ Send user subscribers
        if ($savedBooks > 0) {
            $event = new GenericEvent($date);
            $this->eventDispatcher->dispatch($event,Events::NEW_BOOK_NOTIFY);
        }

        return 1;
    }
}