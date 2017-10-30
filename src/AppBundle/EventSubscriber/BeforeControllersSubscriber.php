<?php

namespace AppBundle\EventSubscriber;

use AppBundle\EventSubscriber\BaseSubscriber;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Firebase\JWT\JWT;

class BeforeControllersSubscriber extends BaseSubscriber
{
    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

	public function checkAuthentication(FilterControllerEvent $event)
    {
        $parameters = [
            'requiredClassName' => 'AppBundle\Contract\AuthRequiredInterface'
        ];

        if (!$this->mustProcess($event, $parameters)) {
            return;
        }

        $headerWithoutBearer = $this->extractHeaderWithoutBearer($event);

        if ($headerWithoutBearer === false) {
            $this->makeResponse($event, 'Unauthorized access.', 401);
        }

        $jwtSecretKey = $this->container->getParameter('secret');

        try {
            JWT::$leeway = 60 * 60 * 24;
            $decodedJWT = JWT::decode($headerWithoutBearer, $jwtSecretKey, ['HS256']);
        }  catch (\Exception $e) {
            $this->makeResponse($event, 'Unauthorized access.', 401);
        }
    }

    public function convertRequestToJSON(FilterControllerEvent $event)
    {
        $parameters = [
            'requiredClassName' => 'AppBundle\Contract\RequestToJsonInterface',
            'ignoreMethods' => ['GET', 'DETELE']
        ];

        if (!$this->mustProcess($event, $parameters)) {
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
            KernelEvents::CONTROLLER => [
                ['convertRequestToJSON', 10],
                ['checkAuthentication', 0]
            ],
        );
    }

    private function extractHeaderWithoutBearer($event)
    {
        $rawHeader = $event->getRequest()->headers->get('Authorization');

        if (strpos($rawHeader, 'Bearer ') === false) {
            return false;
        }

        $headerWithoutBearer = str_replace('Bearer ', '', $rawHeader);

        return $headerWithoutBearer;
    }

    private function hasContentTypeJson($event)
    {
        $contentType = $event->getRequest()->headers->get('Content-Type');

        return false === strpos($contentType, 'application/json');
    }

    private function extractJsonData($event)
    {
        $content = $event->getRequest()->getContent();
        return json_decode($content, true);
    }
}
