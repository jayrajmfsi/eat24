<?php
/**
 *  PlacedOrder Entity
 *  @category Entity
 *  @author Jayraj Arora<jayraja@mindfiresolutions.com>
 */
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PlacedOrder
 *
 * @ORM\Table(name="placed_order")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PlacedOrderRepository")
 * @ORM\HasLifecycleCallbacks()
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
     * @ORM\Column(name="created_date_time", type="datetime")
     */
    private $createdDateTime;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var Restaurant
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Restaurant")
     * @ORM\JoinColumn(name="restaurant_id", referencedColumnName="id")
     */
    private $restaurant;

    /**
     * @var Address
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Address")
     * @ORM\JoinColumn(name="address_id", referencedColumnName="id")
     */
    private $address;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="estimatedDeliveryTime", type="datetime", nullable=true)
     */
    private $estimatedDeliveryTime;

    /**
     * @var integer
     * @ORM\Column(name="order_reference", type="bigint")
     */
    private $orderReference;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="actualDeliveryTime", type="datetime", nullable=true)
     */
    private $actualDeliveryTime;

    /**
     * @var string
     *
     * @ORM\Column(name="totalPrice", type="decimal", precision=12, scale=2, nullable=true)
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
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Saving the unique code before persisting
     * @ORM\PrePersist()
     */
    public function beforeSave()
    {
        $this->createdDateTime = new \DateTime();
        $this->orderReference = Address::generateUniqueId($this->id);
    }

    /**
     * Set orderTime
     *
     * @param \DateTime $orderTime
     *
     * @return PlacedOrder
     */
    public function setCreatedDateTime($orderTime)
    {
        $this->createdDateTime = $orderTime;

        return $this;
    }

    /**
     * Get orderTime
     *
     * @return \DateTime
     */
    public function getCreatedDateTime()
    {
        return $this->createdDateTime;
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
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return Restaurant
     */
    public function getRestaurant()
    {
        return $this->restaurant;
    }

    /**
     * @param Restaurant $restaurant
     */
    public function setRestaurant($restaurant)
    {
        $this->restaurant = $restaurant;
    }

    /**
     * Set orderReference
     *
     * @param integer $orderReference
     *
     * @return PlacedOrder
     */
    public function setOrderReference($orderReference)
    {
        $this->orderReference = $orderReference;

        return $this;
    }

    /**
     * Get orderReference
     *
     * @return integer
     */
    public function getOrderReference()
    {
        return $this->orderReference;
    }

    /**
     * sets address
     * @param Address $address
     * @return PlacedOrder
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * gets address
     * @return Address
     */
    public function getAddress()
    {
        return $this->address;
    }
}
