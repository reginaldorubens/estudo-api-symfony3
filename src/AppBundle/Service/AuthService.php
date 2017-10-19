<?php

namespace AppBundle\Service;

use AppBundle\Service\UserService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface as PasswordEncoder;

class AuthService
{
	private $encoder;
	private $userService; 

	public function __construct(PasswordEncoder $encoder, UserService $userService)
	{
		$this->encoder = $encoder;
		$this->userService = $userService;
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

        return new JsonResponse(['token' => $this->generateToken($user)], 200);
	}

	private function validatePassword(UserInterface $user, $givenPassword)
	{
		return $this->encoder->isPasswordValid($user, $givenPassword);
	}

	private function generateToken(UserInterface $user)
	{
		

		return $token;
	}
}
