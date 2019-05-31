<?php
/**
 *  Service Class for Creating API Request Response.
 *
 *  @category Service
 *  @author <jayraja@mindfiresolutions.com>
 */
namespace AppBundle\Service;

use AppBundle\Constants\ErrorConstants;

/**
 * Setting the failure or success api response
 * Class ApiResponse
 * @package AppBundle\Service
 */
class ApiResponse extends BaseService
{
    /**
     *  Function to create API Error Response.
     *
     *  @param string $errorCode
     *
     *  @return array
     */
    public function createApiErrorResponse($errorCode)
    {
        return [
            'Response' => [
                'reasonCode' => '1',
                'reasonText' => $this->translator->trans('api.response.failure.message'),
                'error' => [
                    'code' => ErrorConstants::$errorCodeMap[$errorCode]['code'],
                    'text' => $this->translator
                        ->trans(ErrorConstants::$errorCodeMap[$errorCode]['message'])
                ],
            ]
        ];
    }

    /**
     *  Function to create final Success User API response.
     *
     *  @param string $responseKey
     *  @param array $data
     *
     *  @return array
     */
    public function createUserApiSuccessResponse($responseKey, $data)
    {
        return [
            'reasonCode' => '0',
            'reasonText' => $this->translator->trans('api.response.success.message'),
            $responseKey => $data,
        ];
    }
}
