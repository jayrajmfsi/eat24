<?php
/**
 *  Class for setting and getting point data type objects
 *  @category utility
 *  @author Jayraj Arora<jayraja@mindfiresolutions.com>
 */
namespace AppBundle\Entity\Utils;

/**
 * Class Point
 * @package AppBundle\Entity\Utils
 */
class Point
{
    /**
     * @var float
     */
    private $latitude;
    /**
     * @var float
     */
    private $longitude;

    /**
     * Setting point object
     * @param float $latitude
     * @param float $longitude
     */
    public function __construct($latitude, $longitude)
    {
        $this->latitude  = $latitude;
        $this->longitude = $longitude;
    }

    /**
     * Get Latitude
     * @return float
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * Get Longitude
     * @return float
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * String representation of object
     * @return string
     */
    public function __toString()
    {
        //Output from this is used with POINT_STR in DQL so must be in specific format
        return sprintf('POINT(%f %f)', $this->longitude, $this->latitude);
    }
}
