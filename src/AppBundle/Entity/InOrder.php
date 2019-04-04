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
    private $placedOrderId;

    /**
     * @var MenuItem
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\MenuItem")
     * @ORM\JoinColumn(name="menu_item_id", referencedColumnName="id")
     */
    private $menuItemId;

    /**
     * @var string
     *
     * @ORM\Column(name="itemPrice", type="decimal", precision=12, scale=2)
     */
    private $itemPrice;


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
     * Set itemPrice
     *
     * @param string $itemPrice
     *
     * @return InOrder
     */
    public function setItemPrice($itemPrice)
    {
        $this->itemPrice = $itemPrice;

        return $this;
    }

    /**
     * Get itemPrice
     *
     * @return string
     */
    public function getItemPrice()
    {
        return $this->itemPrice;
    }

    /**
     * @return PlacedOrder
     */
    public function getPlacedOrderId()
    {
        return $this->placedOrderId;
    }

    /**
     * @param PlacedOrder $placedOrderId
     * @return InOrder
     */
    public function setPlacedOrderId($placedOrderId)
    {
        $this->placedOrderId = $placedOrderId;

        return $this;
    }

    /**
     * @return MenuItem
     */
    public function getMenuItemId()
    {
        return $this->menuItemId;
    }

    /**
     * @param MenuItem $menuItemId
     * @return InOrder
     */
    public function setMenuItemId($menuItemId)
    {
        $this->menuItemId = $menuItemId;

        return $this;
    }

}

