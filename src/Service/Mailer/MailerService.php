<?php

namespace App\Service\Mailer;

use App\Message\SendNotification;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailerService
{
    /**
     * @var MailerInterface
     */
    private $mailer;
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(MailerInterface $mailer, LoggerInterface $logger)
    {
        $this->mailer = $mailer;
        $this->logger = $logger;
    }

    public function sendNewBookNotification(SendNotification $notification): Email
    {
        $email = (new Email())
            ->from('contact@enjoybooks.com')
            ->to($notification->getEmail())
            ->subject('New content')
            ->html('<h1>Notification</h1><p>'.$notification->getMessage().'</p>');

        $this->mailer->send($email);
        $this->logger->info('Email send to :' . $user->getEmail());

        return $email;
    }
}
