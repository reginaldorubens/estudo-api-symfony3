<?php

namespace AppBundle\Controller\Api\v1;

use AppBundle\Contract\RequestToJsonInterface;
use AppBundle\Service\TaskService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class TaskController extends Controller implements RequestToJsonInterface
{
	/**
     * @Route("/api/v1/tasks")
     * @Method("GET")
     */
	public function listAll(TaskService $taskService)
	{
    	return $taskService->listAll();
	}

	/**
     * @Route("/api/v1/tasks/{id}", name="get_task")
     * @Method("GET")
     */
	public function retrieve(TaskService $taskService, $id)
	{
		return $taskService->get($id);
	}

	/**
     * @Route("/api/v1/tasks")
     * @Method("POST")
     */
	public function insert(Request $request, TaskService $taskService)
	{
	   	return $taskService->insert($request);
	}

	/**
     * @Route("/api/v1/tasks/{id}")
     * @Method("PUT")
     */
	public function update(Request $request, TaskService $taskService, $id)
	{
		return $taskService->update($request, $id);
	}

	/**
     * @Route("/api/v1/tasks/{id}")
     * @Method("DELETE")
     */
	public function delete(TaskService $taskService, $id)
	{
		return $taskService->delete($id);
	}	
}
