<?php

namespace AppBundle\Controller\Api\v1;

use AppBundle\Contract\RequestToJsonInterface;
use AppBundle\Service\AuthService;
use AppBundle\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class AuthController extends Controller implements RequestToJsonInterface
{
	/**
     * @Route("/api/v1/login")
     * @Method("POST")
     */
	public function login(Request $request, AuthService $authService)
	{
    	return $authService->login($request);
	}

    /**
     * @Route("/api/v1/initialize")
     * @Method("POST")
     */
    public function initialize(Request $request, UserService $userService)
    {
        return $userService->insert($request);
    }
}
