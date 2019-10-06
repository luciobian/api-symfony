<?php

namespace App\EventSuscriber;

use DateTime;
use App\Entity\PublishedDateEntityInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelEvents;
use ApiPlatform\Core\EventListener\EventPriorities;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;

class PublishedDateEntitySuscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => ['setDatePublished', EventPriorities::PRE_VALIDATE]
        ];
    }

    public function setDatePublished(GetResponseForControllerResultEvent $event)
    {
        $entity = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();
       
        if (!$entity instanceof PublishedDateEntityInterface
            || Request::METHOD_POST !== $method) return;
        
        $entity->setPublished(new DateTime());
    }


}