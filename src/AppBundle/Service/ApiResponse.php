<?php
/**
 *  Service Class for Creating API Request Response.
 *
 *  @category Service
 */
namespace AppBundle\Service;

use AppBundle\Constants\ErrorConstants;

class ApiResponse extends BaseService
{
    /**
     *  Function to create API Error Response.
     *
     *  @param string $errorCode
     *  @param string $transactionId (default = null)
     *
     *  @return array
     */
    public function createApiErrorResponse($errorCode, $transactionId = null)
    {
        $response = [
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

        if (!empty($transactionId)) {
            $response['transactionId'] = $transactionId;
        }
        return $response;
    }

    /**
     *  Function to create response of GET products API.
     *
     *  @param array $requestContent
     *  @param array $products
     *
     *  @return array
     */
    public function createGetProductsApiResponse($requestContent, $products)
    {
        return [
            'Response' => [
                'reasonCode' => '0',
                'reasonText' => $this->translator->trans('api.response.success.message'),
                'satn' => $requestContent['Request']['satn'],
                'merchantId' => $requestContent['Request']['merchantId'],
                'terminalId' => $requestContent['Request']['terminalId'],
                'products' => $products
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

    /**
     *  Function to create final Success User API response.
     *
     *  @param string $responseKey
     *  @param array $user
     *
     *  @return array
     */
    public function getUserApiSuccessResponse($responseKey, $user)
    {
        return [
            'reasonCode' => '0',
            'reasonText' => $this->translator->trans('api.response.success.message'),
            $responseKey => [
                'phoneNumber' => $user['phoneNumber'],
                'username' => $user['username']
            ],
        ];
    }
}
