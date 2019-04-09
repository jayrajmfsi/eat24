<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Utils\Point;
use Doctrine\ORM\Mapping as ORM;

/**
 * Address
 *
 * @ORM\Table(name="address")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AddressRepository")
 */
class Address
{
    const CUSTOMER_ADDRESS = 'USER';
    const RESTAURANT_ADDRESS = 'RESTAURANT';

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
     * @ORM\Column(name="completeAddress", type="text")
     */
    private $completeAddress;

    /**
     * @var Point
     *
     * @ORM\Column(name="geoPoint", type="point", nullable=true)
     */
    private $geoPoint;

    /**
     * @ORM\Column(name="customer_id", type="integer", nullable=false)
     */
    private $customerId;

    /**
     * @var string
     * @ORM\Column(name="address_type", type="string", nullable=false)
     */
    private $addressType;

    /**
     * @var string
     *
     * @ORM\Column(name="city", type="string", length=255, nullable=false)
     */
    private $city;

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
     * Set completeAddress
     *
     * @param string $completeAddress
     *
     * @return Address
     */
    public function setCompleteAddress($completeAddress)
    {
        $this->completeAddress = $completeAddress;

        return $this;
    }

    /**
     * Get completeAddress
     *
     * @return string
     */
    public function getCompleteAddress()
    {
        return $this->completeAddress;
    }

    /**
     * Set geoPoint
     *
     * @param Point $geoPoint
     *
     * @return Address
     */
    public function setGeoPoint($geoPoint)
    {
        $this->geoPoint = $geoPoint;

        return $this;
    }

    /**
     * Get geoPoint
     *
     * @return Point
     */
    public function getGeoPoint()
    {
        return $this->geoPoint;
    }

    /**
     * Set city
     *
     * @param string $city
     *
     * @return Address
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @return string
     */
    public function getAddressType()
    {
        return $this->addressType;
    }

    /**
     * @param string $addressType
     */
    public function setAddressType($addressType)
    {
        $this->addressType = $addressType;
    }

    /**
     * @param string $customerId
     */
    public function setCustomerId($customerId)
    {
        $this->customer = $customerId;
    }

    /**
     * @return string
     */
    public function getCustomerId()
    {
        return $this->customerId;
    }
}

