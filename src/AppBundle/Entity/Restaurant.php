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
}

