<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * InOrder
 *
 * @ORM\Table(name="in_order")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\InOrderRepository")
 */
class InOrder
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="quantity", type="integer")
     */
    private $quantity;

    /**
     * @var PlacedOrder
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\PlacedOrder")
     * @ORM\JoinColumn(name="placed_order_id", referencedColumnName="id")
     */
    private $placedOrder;

    /**
     * @var InRestaurant
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\InRestaurant")
     * @ORM\JoinColumn(name="menu_item_id", referencedColumnName="id")
     */
    private $inRestaurant;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set quantity
     *
     * @param integer $quantity
     *
     * @return InOrder
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @return PlacedOrder
     */
    public function getPlacedOrder()
    {
        return $this->placedOrder;
    }

    /**
     * @param PlacedOrder $placedOrder
     * @return InOrder
     */
    public function setPlacedOrder($placedOrder)
    {
        $this->placedOrder = $placedOrder;

        return $this;
    }

    /**
     * @return InRestaurant
     */
    public function getInRestaurant()
    {
        return $this->inRestaurant;
    }

    /**
     * @param InRestaurant $inRestaurant
     * @return InOrder
     */
    public function setInRestaurant($inRestaurant)
    {
        $this->inRestaurant = $inRestaurant;

        return $this;
    }

}

