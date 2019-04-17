<?php
/**
 *  Service Class for providing functions other than APIs main processing Logic to other Application Services.
 *
 *  @category Service
 *  @author Jayraj Arora<jayraja@mindfiresolutions.com>
 */

namespace AppBundle\Service;

use AppBundle\Constants\ErrorConstants;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\PropertyAccess\PropertyAccess;

class Utils extends BaseService
{
    /**
     * @var int
     */
    private $recursiveCount = 0;

    /**
     *  Function to Trim values in a PHP Array recursively.
     *
     *  @param array $arrayContent
     *
     *  @return array
     *  @throws BadRequestHttpException
     */
    public function trimArrayValues($arrayContent)
    {
        // checking first if $arrayContent passed is empty then returning the input parameter content.
        if (empty($arrayContent)) {
            return $arrayContent;
        }
        // Iterating through array Content and trimming values.
        foreach ($arrayContent as $key => $value) {
            if (is_array($value)) {
                // Increasing recursion count
                $this->recursiveCount++;
                // For Stopping recursive call to go beyond limit.
                if ($this->recursiveCount > 2000) {
                    break;
                }
                // recursive call to function for trimming Array content values.
                $arrayContent[trim($key)] = (!empty($value))
                    ? $this->trimArrayValues($value)
                    : $value
                ;

                // Removing non-trimmed Keys.
                if ((string)trim($key) !== (string)$key) {
                    unset($arrayContent[$key]);
                }
            } elseif (!is_array($value) && !is_object($value)) {
                $arrayContent[trim($key)] = is_string($value)
                    ? ((empty($value = trim($value)) && $value !== "0")
                        ? null
                        : htmlspecialchars($value, ENT_QUOTES, 'UTF-8')) // Handling Html input
                    : htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); // for XSS Prevention

                // Removing non-trimmed Keys From Array.
                if ((string)trim($key) !== (string)$key) {
                    unset($arrayContent[$key]);
                }
            }
        }

        return $arrayContent;
    }

    /**
     *  Function to fill Array data into the Class Object.
     *
     *  @param array $data
     *  @param string $className
     *  @param object $object
     *
     *  @return object
     *  @throws \Exception
     */
    public function createObjectFromArray($data, $className, $object)
    {
        // Checking the Object and Class Name Provided
        if (!empty($object) && !$object instanceof $className) {
            throw new \Exception('Invalid parameters provided to function '.__FUNCTION__);
        }

        if (empty($object)) {
            $resourceClass = new \ReflectionClass($className);
            if (!$resourceClass->isInstantiable()) {
                throw new \Exception($className. ' class name passed to function '.__FUNCTION__.
                    ' is not instantiable');
            }

            $object = $resourceClass->newInstance();
        }

        $propertyAccessor = PropertyAccess::createPropertyAccessor();

        // filling Array data to Object.
        // Note: All the properties of the class should be should be available
        // to be set by Setters.
        foreach ($data as $attribute => $value) {
            if (!$propertyAccessor->isWritable($object, $attribute)) {
                throw new \Exception('Invalid array data provided to function '. __FUNCTION__);
            }
            $propertyAccessor->setValue($object, $attribute, $value);
        }

        return $object;
    }

    /**
     *  Function to validate the pagination array of API content.
     *
     *  @param array $pagination
     *
     *  @return array
     */
    public function validatePaginationArray($pagination)
    {
        $validateResult = [];
        // Validating the pagination parameters.
        $validateResult['page'] = (empty($pagination['pagination']['page'])
            || !ctype_digit($pagination['pagination']['page'])
            || $pagination['pagination']['page'] < 1)
            ? 1
            : $pagination['pagination']['page']
        ;

        $validateResult['limit'] = (empty($pagination['pagination']['limit'])
            || !ctype_digit($pagination['pagination']['limit'])
            || $pagination['pagination']['limit'] < 1)
            ? 10
            : $pagination['pagination']['limit']
        ;

        return $validateResult;
    }

    public function validateRestaurantCode($reference)
    {
        $restaurant = $this->entityManager->getRepository('AppBundle:Restaurant')
            ->findOneBy(['reference' => $reference])
        ;
        if (empty($restaurant)) {
            throw new UnprocessableEntityHttpException(ErrorConstants::INVALID_RESTAURANT_CODE);
        }
        return $restaurant;
    }

    public function validateAddressCode($addressCode, $userId)
    {
        try {
            $address = $this->entityManager->getRepository('AppBundle:Address')
                ->getAddress($userId, $addressCode);

            if (!$address) {
                throw new UnprocessableEntityHttpException(ErrorConstants::INVALID_ADDRESS_CODE);
            }

            return $address;
        } catch (BadRequestHttpException $exception) {
            throw $exception;
        } catch (\Exception $ex) {
            $this->logger->error(__FUNCTION__ . ' Function failed due to Error :' . $ex->getMessage());
            throw new HttpException(500, ErrorConstants::INTERNAL_ERR);
        }
    }
}
