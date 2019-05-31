<?php

/**
 * Exception listener class for the kernel exception
 *
 * @category Listener
 * @author <jayraja@mindfiresolutions.com>
 */
namespace AppBundle\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use AppBundle\Service\BaseService;
use AppBundle\Constants\ErrorConstants;

/**
 * Class ExceptionListener
 * @package AppBundle\EventListener
 */
class ExceptionListener extends BaseService
{
    /**
     * Function for handling exceptions
     *
     * @param GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $status = method_exists($event->getException(), 'getStatusCode')
            ? $event->getException()->getStatusCode()
            : 500;
        $exceptionMessage = $event->getException()->getMessage();

        // logging the error message
        if (!is_array($exceptionMessage) && !in_array($exceptionMessage, array_keys(ErrorConstants::$errorCodeMap))) {
            // Log the Exception Not thrown from controllers because other have been logged Already in controllers.
            $this->logger->error(
                "Error",
                [
                    $status => $event->getException()->getMessage(),
                    'TRACE' => $event->getException()->getTraceAsString()
                ]
            );

        } elseif (is_array($exceptionMessage)) {
            $exceptionMessage = $exceptionMessage['error'];
        }
        // checking the status code and setting the message of exception accordingly
        switch ($status) {
            case 403:
                $messageKey = ErrorConstants::INVALID_AUTHORIZATION;
                break;
            case 404:
                $messageKey = ErrorConstants::RESOURCE_NOT_FOUND;
                break;
            case 405:
                $messageKey = ErrorConstants::METHOD_NOT_ALLOWED;
                break;
            case 408:
                $messageKey = ErrorConstants::REQ_TIME_OUT;
                break;
            case 500:
                $messageKey = ErrorConstants::INTERNAL_ERR;
                break;
            case 503:
                $messageKey = ErrorConstants::SERVICE_UNAVAIL;
                break;
            case 504:
                $messageKey = ErrorConstants::GATEWAY_TIMEOUT;
                break;
            case 400:
            case 401:
            case 502:
            case 422:
            case 409:
                $messageKey = $exceptionMessage;
                break;
            default :
                $messageKey = ErrorConstants::INTERNAL_ERR;
                break;
        }

        $responseService = $this->serviceContainer->get('eat24.api_response_service');
        // Creating Http Error response.
        $result = $responseService->createApiErrorResponse($messageKey);

        $response = new JsonResponse($result, $status);
        // Logging Exception in Exception log.
        $this->logger->error(
            'Eat24 Exception :',
            [
                'Response' => [
                    'Headers' => $response->headers->all(),
                    'Content' => $response->getContent()
                ]
            ]
        );
        $event->setResponse($response);
    }
}
