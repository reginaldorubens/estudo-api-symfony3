<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Task;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class TaskFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        for($i = 0; $i < 20; $i++) {
            $task = new Task();
            $task->setTitle('Tarefa exemplo ' . ($i + 1));

            $manager->persist($task);
        }

        $manager->flush();
    }
}
