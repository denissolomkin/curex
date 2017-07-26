<?php
namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\User;

class LoadUserData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        foreach(['Christoph','Alexander','Christie','Shelly','Karol','Luca','Sandy','Sofia','Kate','Buddy'] as $id => $name){
            $entity = new User();
            $entity->setName($name)
                ->setActive(1);
            $this->addReference('user-'.$id, $entity);
            $manager->persist($entity);
        }

        $manager->flush();
    }
    public function getOrder()
    {
        return 1;
    }
}