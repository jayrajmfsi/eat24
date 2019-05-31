<?php
/**
 *  Command for adding dummy addresses for testing purposes
 *
 *  @category Command
 *  @author Jayraj Arora<jayraja@mindfiresolutions.com>
 */
namespace AppBundle\Command;

use AppBundle\Entity\Restaurant;
use Doctrine\DBAL\Types\Type;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use AppBundle\Entity\Address;
use AppBundle\Entity\Utils\Point;

/**
 * Add an addressing
 * Class AddAddressCommand
 * @package AppBundle\Command
 */
class AddAddressCommand extends ContainerAwareCommand
{
    /**
     * Setting the name of command and options to provide for adding an address
     */
    public function configure()
    {
        $this
            ->setName('eat24:add_address')
            ->setDescription('Add the address using longitude and latitude as inputs')
            ->addOption(
                'longitude',
                'long',
                InputOption::VALUE_REQUIRED,
                'Longitude for the address'
            )
            ->addOption(
                'latitude',
                'lat',
                InputOption::VALUE_REQUIRED,
                'Latitude for the address'
            )
            ->addOption(
                'address',
                'addr',
                InputOption::VALUE_REQUIRED,
                'Complete Address'
            )
            ->addOption(
                'name',
                'name',
                InputOption::VALUE_REQUIRED,
                'Name of restaurant'
            )
        ;
    }

    /**
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        try {

            $container = $this->getContainer();
            Type::addType('point', 'AppBundle\Entity\Utils\PointType');
            $em = $container->get('doctrine.orm.default_entity_manager');
            // configure the point data type
            $em->getConnection()->getDatabasePlatform()
                ->registerDoctrineTypeMapping('point', 'point')
            ;

            $location = new Address();
            $restaurant = new Restaurant();
            $restaurant->setName($input->getOption('name'));
            $em->persist($restaurant);
            $em->flush();

            // set geopoint using the point class
            $location->setGeoPoint(new Point($input->getOption('latitude'), $input->getOption('longitude')));
            $location->setCompleteAddress($input->getOption('address'));
            $location->setAddressType(Address::RESTAURANT_ADDRESS);
            $location->setCustomerId($restaurant->getId());

            $em->persist($location);
            $em->flush();
            $em->clear();

            // Fetch the Address object
            $query = $em->createQuery(
                "SELECT l FROM AppBundle\Entity\Address l WHERE l.addressType='RESTAURANT ORDER BY l.id DESC"
            )->setMaxResults(1);

            /* @var Address */
            $location = $query->getOneOrNullResult();
            // writing output of token on the console
            $token = $location->getToken();
            $output->writeln($token);

        } catch (\Exception $e) {
            $output->writeln($e->getMessage());
        }
    }
}
