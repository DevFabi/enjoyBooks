<?php

namespace App\Events;

use App\Service\Notification\NotifiedUser;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

class NewBookNotifySubscriber implements EventSubscriberInterface
{
    private $notifiedUser;

    public function __construct(NotifiedUser $notifiedUser)
    {
        $this->notifiedUser = $notifiedUser;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            Events::NEW_BOOK_NOTIFY => 'onNewBookAdded',
        ];
    }

    public function onNewBookAdded(GenericEvent $event): void
    {
        $this->notifiedUser->personToNotify($event->getSubject());
    }
}
