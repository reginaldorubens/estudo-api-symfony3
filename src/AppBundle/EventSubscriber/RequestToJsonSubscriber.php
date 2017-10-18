<?php

namespace AppBundle\EventSubscriber;

use AppBundle\Controller\RequestToJsonController;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\JsonResponse;

class RequestToJsonSubscriber implements EventSubscriberInterface
{
	public function onKernelController(FilterControllerEvent $event)
    {
        if ($event->getRequest()->getMethod() == 'GET') {
            return;
        }

        $controller = $this->getFirstController($event);

        if (!($controller instanceof RequestToJsonController)) {
            return;
        }
        
        if ($this->hasContentTypeJson($event)) {
            $this->makeResponse($event, 'Request content is not a valid JSON');
            
            return;
        }

        $content = $this->extractJsonData($event);

        if (empty($content)) {
            $this->makeResponse($event, 'Request content is empty');

            return;
        }

        $event->getRequest()->request->replace($content);
    }

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::CONTROLLER => 'onKernelController',
        );
    }

    private function hasContentTypeJson($event)
    {
        $contentType = $event->getRequest()->headers->get('Content-Type');

        return false === strpos($contentType, 'application/json');
    }

    private function makeResponse($event, $message, $statusCode = 400)
    {
        $event->setController(
            function() {
                return new JsonResponse(['message' => $message], $statusCode);
            }
        );
    }

    private function extractJsonData($event)
    {
        $content = $event->getRequest()->getContent();
        return json_decode($content, true);
    }

    private function getFirstController($event)
    {
        $controllers = $event->getController();

        if (!is_array($controllers)) {
            return null;
        }

        return $controllers[0];
    }
}
