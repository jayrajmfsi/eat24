<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PlacedOrder
 *
 * @ORM\Table(name="placed_order")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PlacedOrderRepository")
 */
class PlacedOrder
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
     * @var \DateTime
     *
     * @ORM\Column(name="orderTime", type="datetime")
     */
    private $orderTime;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $userId;

    /**
     * @var Restaurant
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Restaurant")
     * @ORM\JoinColumn(name="restaurant_id", referencedColumnName="id")
     */
    private $restaurantId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="estimatedDeliveryTime", type="datetime")
     */
    private $estimatedDeliveryTime;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="actualDeliveryTime", type="datetime")
     */
    private $actualDeliveryTime;

    /**
     * @var string
     *
     * @ORM\Column(name="deliveryAddress", type="string", length=255)
     */
    private $deliveryAddress;

    /**
     * @var string
     *
     * @ORM\Column(name="totalPrice", type="decimal", precision=12, scale=2)
     */
    private $totalPrice;

    /**
     * @var string
     *
     * @ORM\Column(name="discount", type="decimal", precision=12, scale=2, nullable=true)
     */
    private $discount;

    /**
     * @var string
     *
     * @ORM\Column(name="finalPrice", type="decimal", precision=12, scale=2)
     */
    private $finalPrice;

    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="text", nullable=true)
     */
    private $comment;

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
     * Set orderTime
     *
     * @param \DateTime $orderTime
     *
     * @return PlacedOrder
     */
    public function setOrderTime($orderTime)
    {
        $this->orderTime = $orderTime;

        return $this;
    }

    /**
     * Get orderTime
     *
     * @return \DateTime
     */
    public function getOrderTime()
    {
        return $this->orderTime;
    }

    /**
     * Set estimatedDeliveryTime
     *
     * @param \DateTime $estimatedDeliveryTime
     *
     * @return PlacedOrder
     */
    public function setEstimatedDeliveryTime($estimatedDeliveryTime)
    {
        $this->estimatedDeliveryTime = $estimatedDeliveryTime;

        return $this;
    }

    /**
     * Get estimatedDeliveryTime
     *
     * @return \DateTime
     */
    public function getEstimatedDeliveryTime()
    {
        return $this->estimatedDeliveryTime;
    }

    /**
     * Set actualDeliveryTime
     *
     * @param \DateTime $actualDeliveryTime
     *
     * @return PlacedOrder
     */
    public function setActualDeliveryTime($actualDeliveryTime)
    {
        $this->actualDeliveryTime = $actualDeliveryTime;

        return $this;
    }

    /**
     * Get actualDeliveryTime
     *
     * @return \DateTime
     */
    public function getActualDeliveryTime()
    {
        return $this->actualDeliveryTime;
    }

    /**
     * Set deliveryAddress
     *
     * @param string $deliveryAddress
     *
     * @return PlacedOrder
     */
    public function setDeliveryAddress($deliveryAddress)
    {
        $this->deliveryAddress = $deliveryAddress;

        return $this;
    }

    /**
     * Get deliveryAddress
     *
     * @return string
     */
    public function getDeliveryAddress()
    {
        return $this->deliveryAddress;
    }

    /**
     * Set totalPrice
     *
     * @param string $totalPrice
     *
     * @return PlacedOrder
     */
    public function setTotalPrice($totalPrice)
    {
        $this->totalPrice = $totalPrice;

        return $this;
    }

    /**
     * Get totalPrice
     *
     * @return string
     */
    public function getTotalPrice()
    {
        return $this->totalPrice;
    }

    /**
     * Set discount
     *
     * @param string $discount
     *
     * @return PlacedOrder
     */
    public function setDiscount($discount)
    {
        $this->discount = $discount;

        return $this;
    }

    /**
     * Get discount
     *
     * @return string
     */
    public function getDiscount()
    {
        return $this->discount;
    }

    /**
     * Set finalPrice
     *
     * @param string $finalPrice
     *
     * @return PlacedOrder
     */
    public function setFinalPrice($finalPrice)
    {
        $this->finalPrice = $finalPrice;

        return $this;
    }

    /**
     * Get finalPrice
     *
     * @return string
     */
    public function getFinalPrice()
    {
        return $this->finalPrice;
    }

    /**
     * Set comment
     *
     * @param string $comment
     *
     * @return PlacedOrder
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @return User
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param User $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    /**
     * @return Restaurant
     */
    public function getRestaurantId()
    {
        return $this->restaurantId;
    }

    /**
     * @param Restaurant $restaurantId
     */
    public function setRestaurantId($restaurantId)
    {
        $this->restaurantId = $restaurantId;
    }

}

