<?php


namespace App\Command;


use App\Service\Authors\GetListOfAuthors;
use App\Service\Books\SaveBook;
use App\Service\GoogleAPI\UploadBooks;
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

    private $getListOfBooks;
    private $em;
    private $saveBook;
    /**
     * @var GetListOfAuthors
     */
    private $listOfAuthors;
    /**
     * @var MessageBusInterface
     */
    private $bus;
    /**
     * @var NotifiedUser
     */
    private $notifiedUser;

    /**
     * UpdateBooksCommand constructor.
     * @param UploadBooks $getListOfBooks
     * @param EntityManagerInterface $em
     * @param SaveBook $saveBook
     * @param GetListOfAuthors $listOfAuthors
     * @param MessageBusInterface $bus
     * @param NotifiedUser $notifiedUser
     */
    public function __construct(UploadBooks $getListOfBooks, EntityManagerInterface $em, SaveBook $saveBook, GetListOfAuthors $listOfAuthors, MessageBusInterface $bus, NotifiedUser $notifiedUser)
    {
        $this->getListOfBooks = $getListOfBooks;
        $this->em = $em;
        $this->saveBook = $saveBook;
        parent::__construct();
        $this->listOfAuthors = $listOfAuthors;
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

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $date = new DateTime('now');
        $output->writeln(date_format($date, 'd/m/Y H:i:s').' : command launch');
        // Get all authors
        $authors = $this->listOfAuthors->getAuthors();

        // Get all books which are not in database
        $books = $this->getListOfBooks->getAllBooks($authors);

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