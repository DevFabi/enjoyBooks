<?php


namespace App\Service\Mailer;


use App\Message\SendNotification;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailerService
{
    /**
     * @var MailerInterface
     */
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * @param SendNotification $notification
     * @return Email
     * @throws TransportExceptionInterface
     */
    public function sendNewBookNotification(SendNotification $notification): Email
    {
        $email = (new Email())
            ->from('contact@enjoybooks.com')
            ->to($notification->getEmail())
            ->subject('New content')
            ->html('<h1>Notification</h1><p>'.$notification->getMessage().'</p>');

        $this->mailer->send($email);

        return $email;
    }

}