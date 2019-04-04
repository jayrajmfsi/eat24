<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Utils\Point;
use Doctrine\ORM\Mapping as ORM;

/**
 * Restaurant
 *
 * @ORM\Table(name="restaurant")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\RestaurantRepository")
 */
class Restaurant
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
     * @var string
     *
     * @ORM\Column(name="address", type="text")
     */
    private $address;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\City")
     * @ORM\JoinColumn(name="city_id", referencedColumnName="id")
     */
    private $city;

    /**
     * @var Point
     * @ORM\Column(name="location", type="point")
     *
     */
    private $location;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Cuisine", inversedBy="restaurants")
     * @ORM\JoinTable(name="restaurant_cuisines")
     */
    private $cuisines;

    public function __construct() {
        $this->cuisines = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set address
     *
     * @param string $address
     *
     * @return Restaurant
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @return City
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param mixed $city
     * @return Restaurant
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Set location
     *
     * @param Point $location
     *
     * @return Restaurant
     */
    public function setLocation(Point $location)
    {
        $this->location = $location;

        return $this;
    }

    /**
     * Get location
     *
     * @return Point
     */
    public function getLocation()
    {
        return $this->location;
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
}

