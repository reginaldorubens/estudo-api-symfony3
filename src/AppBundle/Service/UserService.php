<?php

namespace AppBundle\Service;

use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface as PasswordEncoder;

class UserService
{
	private $em;
	public $encoder;

	public function __construct(EntityManager $em, PasswordEncoder $encoder)
	{
		$this->em = $em;
		$this->encoder = $encoder;
	}

	public function listAll()
	{
		$users = $this->serializeUsers(
    		$this->retriveAllUsers()
    	);

		return new JsonResponse($users);
	}

	public function get($id)
	{
		$user = $this->retrieveUser($id);

    	if (is_null($user)) {
    		return new JsonResponse(['message' => 'Id not found.'], 404);
    	}

    	$user = $this->serializeOneUser($user);

		return new JsonResponse($user);
	}

	public function getByUsername($username)
	{
		$user = $this->retrieveUserByUserName($username);

    	if (is_null($user)) {
    		return new JsonResponse(['message' => 'Username not found.'], 404);
    	}

    	$user = $this->serializeOneUser($user);

		return new JsonResponse($user);
	}

	public function insert($request)
	{
		$userAlreadyRegistered = $this->retrieveUserByUserName($request->request->get('username'));

		if (!is_null($userAlreadyRegistered)) {
			return new JsonResponse('Cannot be inserted. Username already in use.', 400);
		}

		$user = new User();
	   	$user->setUsername($request->request->get('username'));

	   	$hash = $this->encoder->encodePassword($user, 
	   		$request->request->get('password'));
		$user->setPassword($hash);

	   	$user->setActive(1);

		return new JsonResponse($this->saveAndSerializeUser($user), 201);
	}

	public function update($request, $id)
	{
		if (empty($id)) {
			return new JsonResponse(['message' => 'Required id.'], 400);
		}

		$user = $this->retrieveUser($id);

    	if (is_null($user)) {
    		return new JsonResponse(['message' => 'User deleted.'], 404);
    	}

    	$user->setUsername($request->request->get('username'));
	   	$user->setActive($request->request->get('active'));

		return new JsonResponse($this->saveAndSerializeUser($user), 200);
	}

	public function delete($id)
	{
		if (empty($id)) {
			return new JsonResponse(['message' => 'Required id.'], 400);
		}

		$user = $this->retrieveUser($id);

    	if (is_null($user)) {
    		return new JsonResponse(['message' => 'User not found.'], 404);
    	}

    	$this->removeUser($user);

		return new JsonResponse(['message' => 'User deleted.'], 200);
	}

	public function retrieveUser($id)
	{
		return $this->em
    				->getRepository('AppBundle:User')
    				->findOneBy(['id' => $id]);
	}

	public function retrieveUserByUserName($username)
	{
		return $this->em
    				->getRepository('AppBundle:User')
    				->findOneBy(['username' => $username]);
	}

	private function retriveAllUsers()
	{
		return $this->em
    				->getRepository('AppBundle:User')
    				->findAll();
	}

	private function saveUser($user)
	{
	    $this->em->persist($user);
	    $this->em->flush();
	}

	private function removeUser($user)
	{
	    $this->em->remove($user);
	    $this->em->flush();
	}

	private function serializeUsers($users)
	{
		return array_map([$this, 'serializeOneUser'], $users);
	}

	private function serializeOneUser($user)
	{
		$convertedUser = [
			'id' => $user->getId(),
			'username' => $user->getUsername(),
			'active' => $user->getActive()
		];

		return $convertedUser;
	}

	private function saveAndSerializeUser($user)
	{
		$this->saveUser($user);

	    $user = $this->serializeOneUser($user);

	    return $user;
	}
}
