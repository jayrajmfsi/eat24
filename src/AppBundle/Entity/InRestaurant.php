<?php
/**
 *  InRestaurant Entity
 *  @category Entity
 *  @author Jayraj Arora<jayraja@mindfiresolutions.com>
 */
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * InRestaurant
 *
 * @ORM\Table(name="in_restaurant")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\InRestaurantRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class InRestaurant
{
    const ACTIVE = 1;
    const INACTIVE = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="price", type="decimal", precision=12, scale=2)
     */
    private $price;

    /**
     * @var bool
     *
     * @ORM\Column(name="active", type="boolean", nullable=false,length=1,
     * options={"comment":"0 means inactive, 1 means active", "default":"1"})
     */
    private $active;

    /**
     * @var string
     * @ORM\Column(name="item_reference", type="string", nullable=true)
     */
    private $itemReference;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @var MenuItem
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\MenuItem")
     * @ORM\JoinColumn(name="menu_item_id", referencedColumnName="id")
     */
    private $menuItem;

    /**
     * @var Restaurant
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Restaurant")
     * @ORM\JoinColumn(name="restaurant_id", referencedColumnName="id")
     */
    private $restaurant;

    /**
     * Saving the unique code before persisting
     * @ORM\PrePersist()
     */
    public function beforeSave()
    {
        $this->itemReference = Address::generateUniqueId($this->id);
    }
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
     * Set price
     *
     * @param string $price
     *
     * @return InRestaurant
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return string
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set active
     *
     * @param boolean $active
     *
     * @return InRestaurant
     */
    public function setIsActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return bool
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return InRestaurant
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set menuItem
     *
     * @param \AppBundle\Entity\MenuItem $menuItem
     *
     * @return InRestaurant
     */
    public function setMenuItem(\AppBundle\Entity\MenuItem $menuItem = null)
    {
        $this->menuItem = $menuItem;

        return $this;
    }

    /**
     * Get menuItem
     *
     * @return \AppBundle\Entity\MenuItem
     */
    public function getMenuItem()
    {
        return $this->menuItem;
    }

    /**
     * Set restaurant
     *
     * @param \AppBundle\Entity\Restaurant $restaurant
     *
     * @return InRestaurant
     */
    public function setRestaurant(\AppBundle\Entity\Restaurant $restaurant = null)
    {
        $this->restaurant = $restaurant;

        return $this;
    }

    /**
     * Get restaurant
     *
     * @return \AppBundle\Entity\Restaurant
     */
    public function getRestaurant()
    {
        return $this->restaurant;
    }

    /**
     * Returns item reference
     * @return string
     */
    public function getItemReference()
    {
        return $this->itemReference;
    }

    /**
     * Sets the item reference
     * @param string $itemReference
     */
    public function setItemReference($itemReference)
    {
        $this->itemReference = $itemReference;
    }
}
