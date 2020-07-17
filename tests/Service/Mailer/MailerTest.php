<?php

namespace App\Tests\Service;

use App\Message\SendNotification;
use App\Service\Mailer\MailerService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Mailer\MailerInterface;

class MailerTest extends TestCase
{
    public function testSendNewBookNotification()
    {
        $symfonyMailer = $this->createMock(MailerInterface::class);
        $symfonyMailer->expects($this->once())
            ->method('send');

        $message = new SendNotification('tututu', 'tutu@enjoybooks.com');

        $mailer = new MailerService($symfonyMailer);

        $email = $mailer->sendNewBookNotification($message);

        $this->assertSame('New content', $email->getSubject());
        $this->assertCount(1, $email->getTo());

    }
}
