<?php
declare(strict_types = 1);

namespace App\Command;


use App\Entity\Author;
use App\Events\Events;
use App\Service\BookUploader\BookUploaderInterface;
use App\Specifications\CanSaveBooksSpecification;
use App\UseCases\CreateBookUseCase;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
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
    private $canSaveBooksSpecification;
    /**
     * @var BookUploaderInterface
     */
    private $bookUploader;
    /**
     * @var EventDispatcher
     */
    private $eventDispatcher;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var CreateBookUseCase
     */
    private $createBookUseCase;

    public function __construct(BookUploaderInterface $bookUploader, EntityManagerInterface $em, CanSaveBooksSpecification $canSaveBooksSpecification,EventDispatcherInterface $eventDispatcher, LoggerInterface $logger, CreateBookUseCase $createBookUseCase)
    {
        $this->bookUploader = $bookUploader;
        $this->em = $em;
        $this->canSaveBooksSpecification = $canSaveBooksSpecification;
        parent::__construct();
        $this->eventDispatcher = $eventDispatcher;
        $this->logger = $logger;
        $this->createBookUseCase = $createBookUseCase;
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
        $this->logger->info('Command launch '. date_format($date, 'd/m/Y H:i:s'));

        // 1/ Get all authors
        $authors = $this->em->getRepository(Author::class)->findAll();

        // 2/ Get all books from googleAPI which are not in database
        $books = $this->bookUploader->getAllBooks($authors);

        $output->writeln(count($books).' books (not saved)');

        // 3/ Save books in database
        foreach ($books as $bookToSave)
        {
            if ($this->canSaveBooksSpecification->isSatisfiedBy($bookToSave)) {
                $this->createBookUseCase->create($bookToSave);
            }
        }

        // 4/ Send user subscribers
            $event = new GenericEvent($date);
            $this->eventDispatcher->dispatch($event,Events::NEW_BOOK_NOTIFY);

        return 1;
    }
}