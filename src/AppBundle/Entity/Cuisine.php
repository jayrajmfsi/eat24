<?php
/**
 *  Cuisine Entity
 *  @category Entity
 *  @author Jayraj Arora<jayraja@mindfiresolutions.com>
 */
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Cuisine
 *
 * @ORM\Table(name="cuisine")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CuisineRepository")
 */
class Cuisine
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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;


    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Restaurant", mappedBy="cuisines")
     */
    private $restaurants;

    /**
     * Setting restaurants collection
     * Cuisine constructor.
     */
    public function __construct()
    {
        $this->restaurants = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set name
     *
     * @param string $name
     *
     * @return Cuisine
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
