<?php

namespace AppBundle\Command;

use Doctrine\DBAL\Types\Type;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use AppBundle\Entity\Restaurant;
use AppBundle\Entity\Utils\Point;

class TestCommand extends ContainerAwareCommand
{
    public function configure()
    {
        $this
            ->setName('eat24:here')
            ->setDescription('sdsds.')
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        try {

            $container = $this->getContainer();
            Type::addType('point', 'AppBundle\Entity\Utils\PointType');
            $em = $container->get('doctrine.orm.default_entity_manager');
            $em->getConnection()->getDatabasePlatform()->registerDoctrineTypeMapping('point', 'point');

//            $location = new Restaurant();
//
//            $location->setAddress('1600 Amphitheatre Parkway, Mountain View, CA');
//            $location->setLocation(new Point(37.4220761, -122.0845187));
//
//            $em->persist($location);
//            $em->flush();
//            $em->clear();

            // Fetch the Location object
            $query = $em->createQuery("SELECT l FROM AppBundle\Entity\Restaurant l WHERE l.address = '1600 Amphitheatre Parkway, Mountain View, CA'");
            $location = $query->getSingleResult();

            /* @var Point */
            $point = $location->getLocation();
            $output->writeln($point);
        } catch (\Exception $e) {
            $output->writeln($e->getMessage());
        }
    }
}