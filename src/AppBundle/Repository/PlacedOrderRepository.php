<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Address;

/**
 * PlacedOrderRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PlacedOrderRepository extends \Doctrine\ORM\EntityRepository
{
    public function fetchOrders($customerId)
    {
        $qb = $this->createQueryBuilder('placedOrder')
            ->join('placedOrder.restaurant', 'restaurant')
            ->join('placedOrder.address', 'address')
            ->select('placedOrder.createdDateTime')
            ->addSelect('placedOrder.finalPrice')
            ->addSelect('restaurant.name')
            ->addSelect('placedOrder.id')
            ->addSelect('address.completeAddress')
            ->where('placedOrder.user =:user')
            ->andWhere('address.addressType =:type')
            ->setParameters([
                'type' => Address::CUSTOMER_ADDRESS,
                'user' => $customerId
            ])
        ;

        return $qb->getQuery()->getArrayResult();
    }
}
