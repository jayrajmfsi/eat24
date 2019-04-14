<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Utils\Point;
use Doctrine\ORM\Mapping as ORM;

/**
 * Address
 *
 * @ORM\Table(name="address")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AddressRepository")
 * @ORM\HasLifecycleCallbacks()
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
     * @ORM\Column(name="token", type="string", nullable=true)
     */
    private $token;

    /**
     * @var string
     *
     * @ORM\Column(name="map_location", type="string", nullable=true)
     */
    private $mapLocation;

    /**
     * @var string
     *
     * @ORM\Column(name="complete_address", type="text")
     */
    private $completeAddress;

    /**
     * @var Point
     *
     * @ORM\Column(name="geo_point", type="point", nullable=true)
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
     * @ORM\Column(name="nick_name", type="string", nullable=true)
     */
    private $nickName;

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
        $this->customerId = $customerId;
    }

    /**
     * @return string
     */
    public function getCustomerId()
    {
        return $this->customerId;
    }

    /**
     * Set mapLocation
     *
     * @param string $mapLocation
     *
     * @return Address
     */
    public function setMapLocation($mapLocation)
    {
        $this->mapLocation = $mapLocation;

        return $this;
    }

    /**
     * Get mapLocation
     *
     * @return string
     */
    public function getMapLocation()
    {
        return $this->mapLocation;
    }

    /**
     * Set nickName
     *
     * @param string $nickName
     *
     * @return Address
     */
    public function setNickName($nickName)
    {
        $this->nickName = $nickName;

        return $this;
    }

    /**
     * Get nickName
     *
     * @return string
     */
    public function getNickName()
    {
        return $this->nickName;
    }

    /**
     * Set token
     *
     * @param string $token
     *
     * @return Address
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get token
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @ORM\PrePersist()
     */
    public function beforeSave()
    {
        $this->token = self::generateUniqueId($this->id);
    }

     /**
     *  Function to generate a new (Most Probably Unique) Id
     *  @param int $id
     *  @return string
     */
    public static function generateUniqueId($id)
    {
        $count = strlen((string)$id);
        $timestamp = round(microtime(true) * 1000) . mt_rand(10, 99) . '';

        return substr($timestamp, $count) . $id;
    }
}
