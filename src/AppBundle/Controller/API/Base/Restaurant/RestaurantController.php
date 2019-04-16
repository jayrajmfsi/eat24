<?php

namespace AppBundle\Controller\API\Base\Restaurant;

use AppBundle\Constants\ErrorConstants;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Options;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class RestaurantController extends AbstractFOSRestController
{
    /**
     * Fetch the restaurants list within a certain location
     *
     * @Get("/restaurants.{_format}")
     * @Options("/restaurants.{_format}")
     *
     * @param Request $request
     * @return array
     */
    public function fetchRestaurantList(Request $request)
    {
        $logger = $this->container->get('monolog.logger.exception');
        // $response to be returned from API.
        $response = null;
        try {
            $utils = $this->container->get('eat24.utils');
            $content = $utils->trimArrayValues(json_decode(trim($request->getContent()), TRUE));
            // Validating the request content.
            $validationResult = $this->container->get('eat24.restaurant_api_validate_service')
                ->validateFilterRestaurantRequest($content)
            ;

            $content = !empty($validationResult['message']['response'])
                ? $validationResult['message']['response']['content']
                : null
            ;
            // Processing the request and creating the final streamed response to be sent in response.
            $result = $this->container->get('eat24.restaurant_api_processing_service')
                ->processRestaurantFilterRequest($content)
            ;
            // Creating final response Array to be released from API Controller.
            $response = $this->container->get('eat24.api_response_service')
                ->createUserApiSuccessResponse('RestaurantDetailsResponse', $result['data']);
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

    /**
     * Fetch the restaurants list within a certain location
     *
     * @Get("/restaurants/menu.{_format}")
     * @Options("/restaurants/menu.{_format}")
     *
     * @param Request $request
     * @return array
     */
    public function showMenu(Request $request)
    {
        $logger = $this->container->get('monolog.logger.exception');
        // $response to be returned from API.
        $response = null;
        try {
            $content = json_decode($request->getContent(), true);
            // Validating the request content.
            $validationResult = $this->container->get('eat24.restaurant_api_validate_service')
                ->validateListMenuRequest($content)
            ;

            // Processing the request and creating the final streamed response to be sent in response.
            $result = $this->container->get('eat24.restaurant_api_processing_service')
                ->processMenuListRequest($validationResult['response']['restaurant'])
            ;
            // Creating final response Array to be released from API Controller.
            $response = $this->container->get('eat24.api_response_service')
                ->createUserApiSuccessResponse('RestaurantDetailsResponse', $result['data']);
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
