<?php

namespace AppBundle\Controller\API\Base\User;

use AppBundle\Constants\ErrorConstants;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations\Options;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class ProfileController extends AbstractFOSRestController
{
    /**
     * To POST username and password of user and create Access and Refresh token for User.
     *
     * @Post("/oauth.{_format}")
     * @Options("/oauth.{_format}")
     * @param Request $request
     *
     *  @return array
     **/
    public function createOAuthToken(Request $request)
    {
        $logger = $this->container->get('monolog.logger.exception');
        // $response to be returned from API.
        $response = NULL;
        try {
            $utils = $this->container->get('eat24.utils');
            // Trimming Request Content.
            $content = $utils->trimArrayValues(json_decode(trim($request->getContent()), TRUE));
            // Validating the request content.
            $this->container
                ->get('eat24.user_api_validate_service')
                ->validateOAuthRequest($content)
            ;

            $authService = $this->container->get('eat24.authenticate_authorize_service');
            // Processing Request Content and Getting Result.
            $authResult = $authService->processOAuthRequest($content)['message']['response'];

            // Creating and final array of response from API.
            $response = $this->container
                ->get('eat24.api_response_service')
                ->createUserApiSuccessResponse('AuthenticationResponse', $authResult)
            ;
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
     * To POST refresh token of user and Generate a new access token.
     *
     * Creates new Access Token using refresh token and returns in response.
     *
     * @Post("/oauth/refresh.{_format}")
     * @Options("/oauth/refresh.{_format}")
     * @param Request $request
     * @return array
     */
    public function refreshOAuthToken(Request $request)
    {
        $logger = $this->container->get('monolog.logger.exception');
        // $response to be returned from API.
        $response = NULL;
        try {
            $utils = $this->container->get('eat24.utils');
            // Trimming Request Content.
            $content = $utils->trimArrayValues(json_decode(trim($request->getContent()), TRUE));

            // Validating the request content.
            $this->container
                ->get('eat24.user_api_validate_service')
                ->validateOAuthRefreshRequest($content)
            ;

            // Processing Request, Creating and returning final response Array from Controller.
            $response = $this->container
                ->get('eat24.api_response_service')
                ->createUserApiSuccessResponse('AuthenticationResponse',
                    $this->container
                        ->get('eat24.authenticate_authorize_service')
                        ->processOAuthRefreshRequest($content)
                    ['message']['response']
                )
            ;
        } catch (BadRequestHttpException $ex) {
            throw $ex;
        } catch (UnprocessableEntityHttpException $ex) {
            throw $ex;
        } catch (HttpException $ex) {
            throw $ex;
        } catch (\Exception $ex) {
            $logger->error('Function '. __FUNCTION__ . ' Failed due to error: '. $ex->getMessage());
            // Throwing Internal Server Error Response In case of Unknown Errors.
            throw new HttpException(500, ErrorConstants::INTERNAL_ERR);
        }

        return $response;
    }

    /**
     * @Post("/create.{_format}")
     * @Options("/create.{_format}")
     * @param Request $request
     * @return array
     */
    public function createUser(Request $request)
    {
        $logger = $this->container->get('monolog.logger.exception');
        // $response to be returned from API.
        $response = NULL;
        try {
            $utils = $this->container->get('eat24.utils');
            $content = json_decode(trim($request->getContent()), TRUE);
            // Trimming Request Content.
            $content = !empty($content) ? $utils->trimArrayValues($content) : $content;

            // Validating the request content.
            $this->container
                ->get('eat24.user_api_validate_service')
                ->validateCreateUserRequest($content)
            ;

            // Processing the request and creating the final streamed response to be sent in response.
            $this->container
                ->get('eat24.user_api_processing_service')
                ->processCreateUpdateUserRequest($content)
            ;

            // Creating final response Array to be released from API Controller.
            $response = $this->container
                ->get('eat24.api_response_service')
                ->createUserApiSuccessResponse('UserResponse', [
                    'status' => $this->container
                        ->get('translator.default')
                        ->trans('api.response.success.user_created')
                ])
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
     * @Post("/update.{_format}")
     * @Options("/update.{_format}")
     * @param Request $request
     * @return array
     */
    public function updateUser(Request $request)
    {
        $logger = $this->container->get('monolog.logger.exception');
        // $response to be returned from API.
        $response = NULL;
        try {
            $utils = $this->container->get('eat24.utils');
            $content = json_decode(trim($request->getContent()), TRUE);
            // Trimming Request Content.
            $content = !empty($content) ? $utils->trimArrayValues($content) : $content;

            // Validating the request content.
            $validateResult = $this->container
                ->get('eat24.user_api_validate_service')
                ->validateUpdateUserRequest($content, $request->attributes->get('emailId'))
            ;
            $user = $validateResult['message']['response']['user'];

            // Processing the request and creating the final streamed response to be sent in response.
            $this->container
                ->get('eat24.user_api_processing_service')
                ->processCreateUpdateUserRequest($content, $user)
            ;

            // Creating final response Array to be released from API Controller.
            $response = $this->container
                ->get('eat24.api_response_service')
                ->createUserApiSuccessResponse('UserResponse', [
                    'status' => $this->container
                        ->get('translator.default')
                        ->trans('api.response.success.user_updated')
                ])
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
     * @Post("/profile.{_format}")
     * @Options("/profile.{_format}")
     * @param Request $request
     * @return array
     */
    public function getUserDetails(Request $request)
    {
        $logger = $this->container->get('monolog.logger.exception');
        // $response to be returned from API.
        $response = NULL;
        try {
            // Processing the request and creating the final streamed response to be sent in response.
            $profileResut = $this->container
                ->get('eat24.user_api_processing_service')
                ->processGetUserProfileRequest($request->attributes->get('emailId'))
            ;

            // Creating final response Array to be released from API Controller.
            $response = $this->container
                ->get('eat24.api_response_service')
                ->getUserApiSuccessResponse(
                    'UserResponse',
                    $profileResut['message']['response']['profileDetails']
                )
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
     * To create/update address details of user
     *
     * @Post("/address.{_format}")
     * @Put("/address.{_format}")
     * @Options("/oauth.{_format}")
     * @param Request $request
     *
     *  @return array
     **/
    public function createUpdateAddress(Request $request)
    {
        $logger = $this->container->get('monolog.logger.exception');
        // $response to be returned from API.
        $response = NULL;
        try {
            $utils = $this->container->get('eat24.utils');
            // Trimming Request Content.
            $content = $utils->trimArrayValues(json_decode(trim($request->getContent()), TRUE));

            $isUpdate = Request::METHOD_PUT === $request->getMethod();
            // Validating the request content.
            $validatedResult = $this->container
                ->get('eat24.user_api_validate_service')
                ->validateAddUpdateAddressRequest($content, $request->attributes->get('emailId'), $isUpdate)
            ;
            $address = $validatedResult['address'] ? $validatedResult['address'] : null;
            // Processing Request Content and Getting Result.
            $result = $this->container->get('eat24.user_api_processing_service')
                ->processUpdateAddressRequest($content['UserDeliveryAddressRequest'], $validatedResult['user'], $address)
            ;

            $transMessageKey = (false === $isUpdate) ? 'api.response.success.address_added'
                :  'api.response.success.address_updated'
            ;
            // Creating and final array of response from API.
            $response = $this->container
                ->get('eat24.api_response_service')
                ->createUserApiSuccessResponse(
                    'UserDeliveryAddressResponse', [
                    'status' => $this->container->get('translator.default')->trans($transMessageKey),
                    'addressCode' => $result['addressCode']
                ])
            ;
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