<?php

namespace AppBundle\EventSubscriber;

use AppBundle\Controller\Api\v1\AuthController;
use App\Bundle\Contract\AuthRequiredInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Bundle\TwigBundle\Controller\ExceptionController;
use Firebase\JWT\JWT;

class AuthSubscriber implements EventSubscriberInterface
{
    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

	public function onKernelController(FilterControllerEvent $event)
    {
        $controller = $this->getFirstController($event);

        if (!$controller instanceof AuthRequiredInterface) {
            return;
        }

        $rawHeader = $event->getRequest()->headers->get('Authorization');

        if (strpos($rawHeader, 'Bearer ') === false) {
            $event->setController(
                function() {
                    return new JsonResponse(['message' => 'Unauthorized access.'], 401);
                }
            );
        }

        $headerWithoutBearer = str_replace('Bearer ', '', $rawHeader);

        $jwtSecretKey = $this->container->getParameter('secret');

        try {
            JWT::$leeway = 60 * 60 * 24;
            $decodedJWT = JWT::decode($headerWithoutBearer, $jwtSecretKey, ['HS256']);
        }  catch (\Exception $e) {
            $event->setController(
                function() {
                    return new JsonResponse(['message' => 'Unauthorized access.'], 401);
                }
            );
        }
    }

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::CONTROLLER => 'onKernelController',
        );
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
