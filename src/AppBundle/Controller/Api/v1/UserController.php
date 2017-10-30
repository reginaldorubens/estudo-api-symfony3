<?php

namespace AppBundle\Controller\Api\v1;

use AppBundle\Contract\RequestToJsonInterface;
use AppBundle\Contract\AuthRequiredInterface;
use AppBundle\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;


/**
 * @Route("/api/v1/users")
 */
class UserController extends Controller implements RequestToJsonInterface, AuthRequiredInterface
{
	/**
     * @Route("/")
     * @Method("GET")
     */
	public function listAll(UserService $userService)
	{
    	return $userService->listAll();
	}

	/**
     * @Route("/{id}")
     * @Method("GET")
     */
	public function retrieve(UserService $userService, $id)
	{
		return $userService->get($id);
	}

	/**
     * @Route("/")
     * @Method("POST")
     */
	public function insert(Request $request, UserService $userService)
	{
	   	return $userService->insert($request);
	}

	/**
     * @Route("/{id}")
     * @Method("PUT")
     */
	public function update(Request $request, UserService $userService, $id)
	{
		return $userService->update($request, $id);
	}

	/**
     * @Route("/{id}")
     * @Method("DELETE")
     */
	public function delete(UserService $userService, $id)
	{
		return $userService->delete($id);
	}
}
