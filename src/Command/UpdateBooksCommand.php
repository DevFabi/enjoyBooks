<?php
declare(strict_types = 1);

namespace App\Command;


use App\Entity\Author;
use App\Service\Books\SaveBook;
use App\Service\BookUploader\BookUploaderInterface;
use App\Service\Notification\NotifiedUser;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class UpdateBooksCommand extends Command
{
    protected static $defaultName = 'app:update-books';

    private $em;
    private $saveBook;
    /**
     * @var MessageBusInterface
     */
    private $bus;
    /**
     * @var NotifiedUser
     */
    private $notifiedUser;
    /**
     * @var BookUploaderInterface
     */
    private $bookUploader;

    public function __construct(BookUploaderInterface $bookUploader, EntityManagerInterface $em, SaveBook $saveBook,  MessageBusInterface $bus, NotifiedUser $notifiedUser)
    {
        $this->bookUploader = $bookUploader;
        $this->em = $em;
        $this->saveBook = $saveBook;
        parent::__construct();
        $this->bus = $bus;
        $this->notifiedUser = $notifiedUser;
    }

    protected function configure()
    {
        $this
            ->setDescription('Update the list of books')
            ->setHelp('This command allows you update the list of books')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) :int
    {
        $date = new DateTime('now');
        $output->writeln(date_format($date, 'd/m/Y H:i:s').' : command launch');
        // Get all authors
        $authors = $this->em->getRepository(Author::class)->findAll();


        // Get all books which are not in database
        $books = $this->bookUploader->getAllBooks($authors);

        $output->writeln(count($books).' books (not saved)');

        // Save in database
        $savedBooks = $this->saveBook->save($books);

        $output->writeln($savedBooks.' books saved!');

        if ($savedBooks > 0) {
            $emailSend = $this->notifiedUser->personToNotify($date);
            $output->writeln($emailSend.' emails send');
        }

        return 1;
    }
}