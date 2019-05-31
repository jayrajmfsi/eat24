<?php
/**
 *  Class for creating point data type and using functions to convert to and from doctrine objects
 *  @category utility
 *  @author Jayraj Arora<jayraja@mindfiresolutions.com>
 */
namespace AppBundle\Entity\Utils;

use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;

/**
 * Configuration and helper function for point type
 * Class PointType
 * @package AppBundle\Entity\Utils
 */
class PointType extends Type
{
    const POINT = 'point';

    /**
     * Get name of point type
     * @return string
     */
    public function getName()
    {
        return self::POINT;
    }

    /**
     * SQL declaration
     * @param array $fieldDeclaration
     * @param AbstractPlatform $platform
     * @return string
     */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return 'POINT';
    }

    /**
     * Convert values from sql to php
     * @param mixed $value
     * @param AbstractPlatform $platform
     * @return Point|mixed
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        list($longitude, $latitude) = sscanf($value, 'POINT(%f %f)');

        return new Point($latitude, $longitude);
    }

    /**
     * Convert values from php to sql
     * @param mixed $value
     * @param AbstractPlatform $platform
     * @return mixed|string
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if ($value instanceof Point) {
            $value = sprintf('POINT(%F %F)', $value->getLongitude(), $value->getLatitude());
        }

        return $value;
    }

    /**
     * helper function
     * @return mixed
     */
    public function canRequireSQLConversion()
    {
        return true;
    }

    /**
     * convert to database query
     * @param string $sqlExpr
     * @param \Doctrine\DBAL\Platforms\AbstractPlatform $platform
     * @return string
     */
    public function convertToPHPValueSQL($sqlExpr, $platform)
    {
        return sprintf('AsText(%s)', $sqlExpr);
    }

    /**
     * convert to database query
     * @param string $sqlExpr
     * @param AbstractPlatform $platform
     * @return string
     */
    public function convertToDatabaseValueSQL($sqlExpr, AbstractPlatform $platform)
    {
        return sprintf('PointFromText(%s)', $sqlExpr);
    }
}
