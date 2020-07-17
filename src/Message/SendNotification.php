<?php


namespace App\Message;


class SendNotification
{
    private $message;

    private $email;

    public function __construct(string $message, string $email)
    {
        $this->message = $message;

        $this->email = $email;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function getEmail()
    {
        return $this->email;
    }
}