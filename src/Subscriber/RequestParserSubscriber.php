<?php

namespace App\Subscriber;

use App\Factory\RequestParserFactory;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class RequestParserSubscriber implements EventSubscriberInterface
{
    /**
     * @return string[][]
     */
    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::CONTROLLER => ['onKernelController']];
    }

    /**
     * @param ControllerEvent $controllerEvent
     */
    public function onKernelController(ControllerEvent $controllerEvent)
    {
        $request = $controllerEvent->getRequest();
        $parser = RequestParserFactory::getParser($request);
        $parser?->parse($request);
    }
}