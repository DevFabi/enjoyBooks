<?php


namespace App\Events;

/**
 * This class defines the names of all the events dispatched in project
 */
final class Events
{
    /**
     * @Event("Symfony\Component\EventDispatcher\GenericEvent")
     *
     * @var string
     */
    const NEW_BOOK_NOTIFY = 'new.book.notify';
}