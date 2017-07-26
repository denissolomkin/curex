<?php
namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Payment;

class LoadPaymentData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $curencies = array('EUR','USD','UAH');

        /* successfull payments */
        for($i = 0; $i < 1000; $i++){
            $date = new \DateTime('now');
            $entity = new Payment();
            $entity->setUser( $this->getReference('user-'.$i%10))
                ->setStatus(1)
                ->setAmount(rand(10,1000))
                ->setCreatedAt($date->modify('- '. $i%7 .' day'))
                ->setCurrencyCode($curencies[($i%3)]);
            $manager->persist($entity);
        }

        $date = new \DateTime('now');

        /* force unsuccessfull eur payments */
        for($i = 0; $i < 5; $i++){
            $entity = new Payment();
            $entity->setUser( $this->getReference('user-'.$i%10))
                ->setStatus(0)
                ->setAmount(rand(10,1000))
                ->setCreatedAt($date)
                ->setCurrencyCode('EUR');
            $manager->persist($entity);
        }

        $manager->flush();
    }

    public function getOrder()
    {
        return 2;
    }
}