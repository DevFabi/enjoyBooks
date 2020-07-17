<?php


namespace App\MessageHandler;


use App\Message\SendNotification;
use App\Service\Mailer\MailerService;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class SendNotificationHandler implements MessageHandlerInterface
{

    /**
     * @var MailerService
     */
    private $mailerService;

    public function __construct(MailerService $mailerService)
    {
        $this->mailerService = $mailerService;
    }

    public function __invoke(SendNotification $notification)
    {
        $this->mailerService->sendNewBookNotification($notification);
    }
}