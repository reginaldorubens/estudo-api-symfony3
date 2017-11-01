<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setUsername('reginaldo');

        $encoder = $this->container->get('security.password_encoder');
        $hash = $encoder->encodePassword($user, '123456');
        $user->setPassword($hash);

        $user->setActive(1);

        $manager->persist($user);
        $manager->flush();
    }
}
