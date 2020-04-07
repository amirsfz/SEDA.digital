<?php

declare(strict_types=1);

namespace SEDAdigital\Tracing;

use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class ConsoleSubscriber implements EventSubscriberInterface
{
    /**
     * @var Tracing
     */
    private $tracing;

    public static function getSubscribedEvents(): array
    {
        return [
            ConsoleEvents::COMMAND => ['onCommand'],
        ];
    }

    public function __construct(Tracing $tracing)
    {
        $this->tracing = $tracing;
    }

    public function onCommand(ConsoleCommandEvent $event): void
    {
        $this->tracing->handleCLICommand($event->getCommand(), $event->getInput());
    }
}
