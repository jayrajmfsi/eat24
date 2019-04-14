<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Restaurant
 *
 * @ORM\Table(name="restaurant")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\RestaurantRepository")
 * @ORM\HasLifecycleCallbacks()
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
     * @var string
     * @ORM\Column(name="reference", type="string", nullable=true)
     */
    private $reference;

    /**
     * @var string
     * @ORM\Column(name="image_file_name", type="string", nullable=true)
     */
    private $imageFileName;

    /**
     * @var float
     * @ORM\Column(type="decimal", precision=2, scale=1, nullable=true)
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
        $this->reference = Address::generateUniqueId($this->id);
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

    /**
     * Set imagePath
     *
     * @param string $imageFileName
     *
     * @return Restaurant
     */
    public function setImageFileName($imageFileName)
    {
        $this->imageFileName = $imageFileName;

        return $this;
    }

    /**
     * Get imageFileName
     *
     * @return string
     */
    public function getImageFileName()
    {
        return $this->imageFileName;
    }

    /**
     * Set reference
     *
     * @param string $reference
     *
     * @return Restaurant
     */
    public function setReference($reference)
    {
        $this->reference = $reference;

        return $this;
    }

    /**
     * Get reference
     *
     * @return string
     */
    public function getReference()
    {
        return $this->reference;
    }
}
