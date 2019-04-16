<?php

namespace AppBundle\Controller\API\Base\User;

use AppBundle\Constants\ErrorConstants;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Options;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class OrderController extends AbstractFOSRestController
{
    public function __construct()
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: content-type, Authorization');
        header('Content-Type: application/json; charset=UTF-8');
        header("Access-Control-Allow-Methods: GET, PUT, POST, DELETE");
    }

    /**
     * Create an order for a particular user
     *
     * @Post("/users/orders.{_format}")
     * @Options("/users/orders.{_format}")
     *
     * @param Request $request
     * @return array
     */
    public function createOrder(Request $request)
    {
        $logger = $this->container->get('monolog.logger.exception');
        // $response to be returned from API.
        $response = null;
        try {

            $utils = $this->container->get('eat24.utils');
            $content = $utils->trimArrayValues(json_decode(trim($request->getContent()), true));
            // Validating the request content.
            $validationResult = $this->container->get('eat24.user_api_validate_service')
                ->validateCreateOrderRequest($content)
            ;

            $result = $this->container->get('eat24.user_api_processing_service')
                ->processCreateOrderRequest($validationResult['message']['response'], $content['orderRequest'])
            ;
            // Creating final response Array to be released from API Controller.
            $response = $this->container->get('eat24.api_response_service')
                ->createUserApiSuccessResponse(
                    'OrderDetailsResponse', [
                        'status' => $this->container->get('translator')->trans('api.response.success.order_booked'),
                        'transactionId' => $result['ref']
                ]
            );
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


    /**
     * @Get("/users/orders.{_format}")
     * @Options("/users/orders.{_format}")
     * @return |null
     */
    public function listOrders()
    {
        $response = null;
        $logger = $this->container->get('monolog.logger.exception');
        try {
            $result = $this->container->get('eat24.user_api_processing_service')->processListOrdersRequest();

            $response = $this->container->get('eat24.api_response_service')
                ->createUserApiSuccessResponse(
                    'OrderListResponse', $result['message']['response']
                );
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
