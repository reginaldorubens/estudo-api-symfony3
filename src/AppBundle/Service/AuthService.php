<?php

namespace AppBundle\Service;

use AppBundle\Service\UserService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface as PasswordEncoder;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Firebase\JWT\JWT;

class AuthService
{
	private $encoder;
	private $userService; 
	private $container;

	public function __construct(PasswordEncoder $encoder, UserService $userService, 
		Container $container)
	{
		$this->encoder = $encoder;
		$this->userService = $userService;
		$this->container = $container;
	}

	public function login(Request $request)
	{
		$user = $this->userService->retrieveUserByUserName($request->request->get('username'));

        if (is_null($user)) {
            return new JsonResponse(['message' => 'Unauthorized. Username not found.'], 401);
        }

        if (!$this->validatePassword($user, $request->request->get('password'))) {
            return new JsonResponse(['message' => 'Unauthorized. Wrong password.'], 401);
        }

        if (!$user->getActive()) {
        	return new JsonResponse(['message' => 'Unauthorized. The user is inactive.'], 401);
        }

        return new JsonResponse(['token' => $this->generateToken($user)], 200);
	}

	private function validatePassword(UserInterface $user, $givenPassword)
	{
		return $this->encoder->isPasswordValid($user, $givenPassword);
	}

	private function generateToken(UserInterface $user)
	{
		$jsonObject = [
            "iss" => "reginaldorubens",
            "aud" => "https://github.com/reginaldorubens/test-api-restful-silex",
            "iat" => time(), // Issued At Time
            "nbf" => time(), // Not Before Time
            "exp" => time() + 60 * 60 * 24, // Expiration Time (24 hours)
            "payload" => $this->formatTokenPayload($user)
        ];

        $jwtSecretKey = $this->container->getParameter('secret');
        
        $token = JWT::encode($jsonObject, $jwtSecretKey);

		return $token;
	}

	private function formatTokenPayload($user)
	{
		$payload = [
			'id' => $user->getId(),
			'username' => $user->getUsername()
		];

		return $payload;
	}
}
