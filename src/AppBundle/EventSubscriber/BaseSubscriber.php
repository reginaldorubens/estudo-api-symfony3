<?php

namespace AppBundle\EventSubscriber;

use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class BaseSubscriber implements EventSubscriberInterface
{
    protected $ignoreMethods = [];
    protected $requiredClassName = '';

    protected function makeResponse($event, $message, $statusCode = 400)
    {
        $event->setController(
            function() use ($message, $statusCode) {
                return new JsonResponse(['message' => $message], $statusCode);
            }
        );
    }

    protected function getFirstController($event)
    {
        $controllers = $event->getController();

        if (!is_array($controllers)) {
            return null;
        }

        return $controllers[0];
    }

    protected function mustProcess($event, $parameters)
    {
        if (array_key_exists('ignoreMethods', $parameters) &&
            count($parameters['ignoreMethods']) > 0 &&
            in_array($event->getRequest()->getMethod(), $parameters['ignoreMethods'])) {

            return false;
        }

        if (array_key_exists('requiredClassName', $parameters) &&
            !empty($parameters['requiredClassName']) &&
            !is_subclass_of($this->getFirstController($event), $parameters['requiredClassName'])) {

            return false;
        }

        return true;
    }

    public static function getSubscribedEvents()
    {
        return [];
    }
}
