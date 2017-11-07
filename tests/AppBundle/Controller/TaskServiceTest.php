<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Service\TaskService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManager;

class TaskServiceTest extends WebTestCase
{
    private $taskService;
    private $em;

    public function setUp()
    {
        self::bootKernel();

        $container = static::$kernel->getContainer();

        $this->em = $container->get('doctrine')->getManager();

        $this->taskService = new TaskService($this->em, $container);
    }

    public function testInsert()
    {
        $request = new Request();
        $request->request->replace(['title' => 'Testing inclusion']);

        $jsonResult = $this->taskService->insert($request);
        $taskFromJsonResult = json_decode($jsonResult->getContent());

        $lastId = $taskFromJsonResult->id;

        $this->assertSame(201, $jsonResult->getStatusCode());
        $this->assertSame('Testing inclusion', $taskFromJsonResult->title);

        return $lastId;
    }

    /**
     * @depends testInsert
     */
    public function testListAll()
    {
        $jsonResult = $this->taskService->listAll();
        $arrayFromJsonResult = json_decode($jsonResult->getContent());

        $this->assertGreaterThan(0, count($arrayFromJsonResult));

        $this->assertSame(200, $jsonResult->getStatusCode());
    }

    /**
     * @depends testInsert
     */
    public function testGet($id)
    {
        $jsonResult = $this->taskService->get($id);

        $taskFromJsonResult = json_decode($jsonResult->getContent());

        $this->assertSame(200, $jsonResult->getStatusCode());
        $this->assertSame('Testing inclusion', $taskFromJsonResult->title);
        $this->assertSame($id, $taskFromJsonResult->id);

        $jsonResult = $this->taskService->get(9999999);
        $decodedJsonResult = json_decode($jsonResult->getContent());

        $this->assertSame(404, $jsonResult->getStatusCode());
        $this->assertSame('Id not found.', $decodedJsonResult->message);
    }
}
