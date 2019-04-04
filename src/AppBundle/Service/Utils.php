<?php
/**
 *  Service Class for providing functions other than APIs main processing Logic to other Application Services.
 *
 *  @category Service
 *  @author Ashish Kumar<ashish.k@mindfiresolutions.com>
 */

namespace AppBundle\Service;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use AppBundle\Constants\ErrorConstants;
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
     * Function to check if String($haystack) ends with SubString($needle)
     *
     * @param $haystack
     * @param $needle
     *
     * @return bool
     */
    public function endsWith($haystack, $needle)
    {
        $length = strlen($needle);

        return $length === 0 ||
            (substr($haystack, -$length) === $needle);
    }

    /**
     *  Function to convert comma(,) seperated records array into key value pairs.
     *
     *  @param array $content
     *  @param array $attributes
     *  @param string $uniqueAttributeIndex
     *
     *  @return array
     */
    public function convertArrayRecordsToAttributeData($content, $attributes, $uniqueAttributeIndex)
    {
        $processingResult['status'] = false;
        try {
            $transactions = [];
            $noAttributes = count($attributes);
            foreach ($content as $item) {
                $itemDetails = explode(',', $item);
                if ($noAttributes !== count($itemDetails)) {
                    throw new \Exception("Number of attributes mismatch for Record: $item");
                }

                if (!isset($itemDetails[$uniqueAttributeIndex])) {
                    throw new \Exception("Invalid Unique Attribute Index provided : $uniqueAttributeIndex");
                }
                // Combining Array Key of Attributes and Values of $itemDeails.
                $transactions[$itemDetails[$uniqueAttributeIndex]] = array_combine($attributes, $itemDetails);
            }

            $processingResult['message']['response'] = [
                'transactions' => $transactions
            ];
        } catch (\Exception $ex) {
            $this->logger->error('Function to Convert Array Records to Attributes Key Value Pairs Data
                Failed due to Error: '. $ex->getMessage());
        }
        return $processingResult;
    }

    /**
     *  Function to Convert Array of Associative records to Hash Map
     *  on the basis of provided keyName.
     *
     *  @param string $keyName
     *  @param array $content
     *
     *  @return array
     */
    public function convertArrayRecordsToHashMap($keyName, $content)
    {
        $updatedContent = [];
        foreach ($content as $record) {
            if (isset($record[$keyName])) {
                $updatedContent[$record[$keyName]] = $record;
            }
        }

        return $updatedContent;
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

    /**
     *  Function to Write(Create/Append) the data to File.
     *
     *  @param string $fileBaseDir
     *  @param string $fileName
     *  @param string $content
     *  @param boolean $isAppend
     *
     *  @return string
     */
    public function writeContentToFile($fileBaseDir, $fileName, $content, $isAppend = false)
    {
        $fs = new Filesystem();
        $file = $this->serviceContainer->get('kernel')->getRootDir(). '/..'.
            $fileBaseDir.$fileName;

        // Checking if $isAppend is set to true.
        if (true === $isAppend) {
            $fs->appendToFile($file, $content);
        } else {
            $fs->dumpFile($file, $content);
        }

        return $file;
    }

    /**
     *  Function to create StreamedResponse for a File
     *  and then removing the file from Server after writing it to response.
     *
     *  @param string $file
     *
     *  @return StreamedResponse
     *  @throws \Exception
     */
    public function createFileStreamedResponse($file)
    {
        // Checking if the file name passed exists or not.
        if (!is_file($file)) {
            throw new \Exception('FileName Passed to function '. __FUNCTION__ .
                ' is not present on server.');
        }

        $fs = new Filesystem();
        $response = new StreamedResponse(function () use ($file, $fs) {
            $handle = fopen($file, 'r');
            while (!feof($handle)) {
                // reading 1024 * 1024 KB(1 MB) at a time from File and writing to response.
                $buffer = fread($handle, 1024 * 1024);
                echo $buffer;
                flush();
            }
            fclose($handle);
            // Removing file after writing to response.
            $fs->remove($file);
        });

        $response->headers->set('Content-Description', 'File Transfer');
        $response->headers->set('Content-Type', 'application/octet-stream');
        $response->headers->set('Content-Disposition', 'attachment; filename="'.basename($file).'"');
        $response->headers->set('Expires', '0');
        $response->headers->set('Cache-Control', 'must-revalidate');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Content-Type', 'application/octet-stream');
        return $response;
    }

    /**
     * Function to validate  input date
     * @param string $date
     * @param string $format (default = 'Y-m-d')
     *
     * @return bool
     * @throws \Exception
     */
    public function validateDate($date, $format = 'Y-m-d'): bool
    {
        $d = \DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }

    /**
     * Function to validate  sort attributes of request
     * @param string $entity
     * @param array $sort
     *
     * @return null|array
     * @throws \Exception
     */
    public function validateSort($entity, $sort = null)
    {
        $resourceClass = new \ReflectionClass('B2BEloadBundle\Entity\\'.$entity);

        if (
            2 !== count($sort)
            || !is_string($sort[0])
            || 30 < strlen($sort[0])
            || !isset($sort[0], $resourceClass->getStaticProperties()['allowedSortingAttributesMap'])
        ) {
            return null;
        }

        $sort[0] = $resourceClass->getStaticProperties()['allowedSortingAttributesMap'][$sort[0]];
        $sort[1] = in_array($sort[1], ['ASC','DESC']) ? $sort[1] : 'ASC';

        return $sort;
    }
}
