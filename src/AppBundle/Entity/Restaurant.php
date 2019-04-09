<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Restaurant
 *
 * @ORM\Table(name="restaurant")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\RestaurantRepository")
 */
class Restaurant
{
    public static $allowedSortingAttributesMap = [
        'cost' => 'cost',
        'recentlyAdded' => 'createdDateTime',
        'restaurantRating' => 'rating',
    ];
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
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Cuisine", inversedBy="restaurants")
     * @ORM\JoinTable(name="restaurant_cuisines")
     */
    private $cuisines;

    public function __construct() {
        $this->cuisines = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * @var float
     * @ORM\Column(name="cost", type="integer", nullable=true)
     */
    private $cost;

    /**
     * @var float
     * @ORM\Column(type="decimal", precision=2, scale=1)
     */
    private $rating;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $createdDateTime;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastUpdatedDateTime;

    /**
     * Get id
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    public function addCuisine(Cuisine $cuisine)
    {
        $this->cuisines[] = $cuisine;

        return $this;
    }

    public function removeCuisine(Cuisine $cuisine)
    {
        $this->cuisines->removeElement($cuisine);
    }

    /**
     * Get cuisines
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCuisines()
    {
        return $this->cuisines;
    }

    /**
     * @param string string
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set cost
     *
     * @param integer $cost
     *
     * @return Restaurant
     */
    public function setCost($cost)
    {
        $this->cost = $cost;

        return $this;
    }

    /**
     * Get cost
     *
     * @return integer
     */
    public function getCost()
    {
        return $this->cost;
    }

    /**
     * Set rating
     *
     * @param string $rating
     *
     * @return Restaurant
     */
    public function setRating($rating)
    {
        $this->rating = $rating;

        return $this;
    }

    /**
     * Get rating
     *
     * @return string
     */
    public function getRating()
    {
        return $this->rating;
    }


    /**
     * @ORM\PrePersist()
     */
    public function beforeSave()
    {
        $this->createdDateTime = new \DateTime();
    }

    /**
     * @ORM\PreUpdate()
     */
    public function beforeUpdate()
    {
        $this->lastUpdatedDateTime = new \DateTime();
    }

    /**
     * Set createdDateTime
     *
     * @param \DateTime $createdDateTime
     *
     * @return Restaurant
     */
    public function setCreatedDateTime($createdDateTime)
    {
        $this->createdDateTime = $createdDateTime;

        return $this;
    }

    /**
     * Get createdDateTime
     *
     * @return \DateTime
     */
    public function getCreatedDateTime()
    {
        return $this->createdDateTime;
    }

    /**
     * Set lastUpdatedDateTime
     *
     * @param \DateTime $lastUpdatedDateTime
     *
     * @return Restaurant
     */
    public function setLastUpdatedDateTime($lastUpdatedDateTime)
    {
        $this->lastUpdatedDateTime = $lastUpdatedDateTime;

        return $this;
    }

    /**
     * Get lastUpdatedDateTime
     *
     * @return \DateTime
     */
    public function getLastUpdatedDateTime()
    {
        return $this->lastUpdatedDateTime;
    }
}
