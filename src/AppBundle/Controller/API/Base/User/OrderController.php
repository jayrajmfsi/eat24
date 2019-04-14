<?php

namespace AppBundle\Controller\API\Base\User;

use AppBundle\Constants\ErrorConstants;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Options;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class OrderController extends AbstractFOSRestController
{
    /**
     * Fetch the restaurants list within a certain location
     *
     * @Post("/create.{_format}")
     * @Options("/create.{_format}")
     *
     * @param Request $request
     * @return array
     */
    public function createUserOrder(Request $request)
    {
        $logger = $this->container->get('monolog.logger.exception');
        // $response to be returned from API.
        $response = NULL;
        try {
            $utils = $this->container->get('eat24.utils');
            $content = $utils->trimArrayValues(json_decode(trim($request->getContent()), TRUE));
            // Validating the request content.
            $validationResult = $this->container->get('eat24.restaurant_api_validate_service')
                ->validateCreateOrderRequest($content)
            ;

            $result = $this->container->get('eat24.user_api_processing_service')
                ->processCreateOrderRequest($validationResult)
            ;
            // Creating final response Array to be released from API Controller.
            $response = $this->container->get('eat24.api_response_service')
                ->createUserApiSuccessResponse('OrderDetailsResponse', $result['data']);
            ;
        } catch (AccessDeniedHttpException $ex) {
            throw $ex;
        } catch (BadRequestHttpException $ex) {
            throw $ex;
        } catch (UnprocessableEntityHttpException $ex) {
            throw $ex;
        } catch (HttpException $ex) {
            throw $ex;
        } catch (\Exception $ex) {
            $logger->error(__FUNCTION__.' function failed due to Error : '.
                $ex->getMessage());
            // Throwing Internal Server Error Response In case of Unknown Errors.
            throw new HttpException(500, ErrorConstants::INTERNAL_ERR);
        }

        return $response;
    }
}