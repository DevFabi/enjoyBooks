<?php


namespace App\Service\Notification;


use DateTime;

interface NotificationInterface
{

    public function personToNotify(DateTime $date);
    public function dispatchNotification($message, $email);

}