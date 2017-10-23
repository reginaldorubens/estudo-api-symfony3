<?php

namespace AppBundle\Service;

use AppBundle\Entity\Task;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;

class TaskService
{
	private $em;
	private $container;

	public function __construct(EntityManager $em, Container $container)
	{
		$this->em = $em;
		$this->container = $container;
	}

	public function listAll()
	{
		$tasks = $this->serializeTasks(
    		$this->retriveAllTasks()
    	);

		return new JsonResponse($tasks);
	}

	public function get($id)
	{
		$task = $this->retrieveTask($id);

    	if (is_null($task)) {
    		return new JsonResponse(['message' => 'Id not found.'], 404);
    	}

    	$task = $this->serializeOneTask($task);

		return new JsonResponse($task);
	}

	public function insert($request)
	{
		$task = new Task();
	   	$task->setTitle($request->request->get('title'));
	   	$task = $this->saveAndSerializeTask($task);

	   	$urlToCreatedTask = $this->container->get('router')
	   		->generate('get_task', ['id' => $task['id']], UrlGeneratorInterface::ABSOLUTE_URL);

	   	$response = new JsonResponse($task, 201);
	   	$response->headers->set('Location', $urlToCreatedTask);

		return $response;
	}

	public function update($request, $id)
	{
		if (empty($id)) {
			return new JsonResponse(['message' => 'Required id.'], 400);
		}

		$task = $this->retrieveTask($id);

    	if (is_null($task)) {
    		return new JsonResponse(['message' => 'Task deleted.'], 404);
    	}

    	$task->setTitle($request->request->get('title'));

		return new JsonResponse($this->saveAndSerializeTask($task), 200);
	}

	public function delete($id)
	{
		if (empty($id)) {
			return new JsonResponse(['message' => 'Required id.'], 400);
		}

		$task = $this->retrieveTask($id);

    	if (is_null($task)) {
    		return new JsonResponse(['message' => 'Task not found.'], 404);
    	}

    	$this->removeTask($task);

		return new JsonResponse(['message' => 'Task deleted.'], 200);
	}

	private function retrieveTask($id)
	{
		return $this->em
    				->getRepository('AppBundle:Task')
    				->findOneBy(['id' => $id]);
	}

	private function retriveAllTasks()
	{
		return $this->em
    				->getRepository('AppBundle:Task')
    				->findAll();
	}

	private function saveTask($task)
	{
	    $this->em->persist($task);
	    $this->em->flush();
	}

	private function removeTask($task)
	{
	    $this->em->remove($task);
	    $this->em->flush();
	}

	private function serializeTasks($tasks)
	{
		return array_map([$this, 'serializeOneTask'], $tasks);
	}

	private function serializeOneTask($task)
	{
		$convertedTask = [
			'id' => $task->getId(),
			'title' => $task->getTitle()
		];

		return $convertedTask;
	}

	private function saveAndSerializeTask($task)
	{
		$this->saveTask($task);

	    $task = $this->serializeOneTask($task);

	    return $task;
	}
}
