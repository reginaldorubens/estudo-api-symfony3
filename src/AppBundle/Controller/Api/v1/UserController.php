<?php

namespace AppBundle\Controller\Api\v1;

use AppBundle\Contract\RequestToJsonInterface;
use AppBundle\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class UserController extends Controller implements RequestToJsonInterface
{
	/**
     * @Route("/api/v1/user")
     * @Method("GET")
     */
	public function listAll(UserService $userService)
	{
    	return $userService->listAll();
	}

	/**
     * @Route("/api/v1/user/{id}")
     * @Method("GET")
     */
	public function retrieve(UserService $userService, $id)
	{
		return $userService->get($id);
	}

	/**
     * @Route("/api/v1/user")
     * @Method("POST")
     */
	public function insert(Request $request, UserService $userService)
	{
	   	return $userService->insert($request);
	}

	/**
     * @Route("/api/v1/user/{id}")
     * @Method("PUT")
     */
	public function update(Request $request, UserService $userService, $id)
	{
		return $userService->update($request, $id);
	}

	/**
     * @Route("/api/v1/user/{id}")
     * @Method("DELETE")
     */
	public function delete(UserService $userService, $id)
	{
		return $userService->delete($id);
	}	
}
