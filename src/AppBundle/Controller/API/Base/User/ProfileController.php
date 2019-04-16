<?php

namespace AppBundle\Controller\API\Base\User;

use AppBundle\Constants\ErrorConstants;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Options;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Swagger\Annotations as SWG;

class ProfileController extends AbstractFOSRestController
{
    /**
     * To POST username and password of user and create Access and Refresh token for User.
     *
     * @Post("/login.{_format}")
     * @Options("/login.{_format}")
     *
     * @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     required=true,
     *     @SWG\Schema(
     *          @SWG\Property(
     *              property="credentials",
     *              type="object",
     *                  @SWG\Property(
     *                      property="emailId",
     *                      type="string",
     *                      example="jayraj.arora@gmail.com"
     *                  ),
     *                  @SWG\Property(
     *                      property="password",
     *                      type="string",
     *                      example="<Password Here>"
     *                  )
     *          )
     *     )
     *  )
     *
     * @SWG\Response(
     *     response=200,
     *     description="Login api returning back access and refresh token.",
     *     @SWG\Schema(
     *          @SWG\Property(
     *              property="reasonCode",
     *              type="string",
     *              example="0"
     *          ),
     *          @SWG\Property(
     *              property="reasonText",
     *              type="string",
     *              example="Success"
     *          ),
     *          @SWG\Property(
     *              property="AuthenticationResponse",
     *              type="object",
     *              @SWG\Property(
     *                  property="accessToken",
     *                  type="string",
     *                  example="string"
     *              ),
     *              @SWG\Property(
     *                  property="refresToken",
     *                  type="string",
     *                  example="string"
     *              )
     *          )
     *      )
     *  )
     * @SWG\Response(
     *     response=400,
     *     description="Bad Request.",
     *     @SWG\Schema(
     *          @SWG\Property(
     *              property="reasonCode",
     *              type="string",
     *              example="1"
     *          ),
     *          @SWG\Property(
     *              property="reasonText",
     *              type="string",
     *              example="failure"
     *          ),
     *          @SWG\Property(
     *              property="error",
     *              type="object",
     *              @SWG\Property(
     *                  property="code",
     *                  type="integer",
     *                  example="1014"
     *              ),
     *              @SWG\Property(
     *                  property="text",
     *                  type="string",
     *                  example="Invalid Credentials found in Request Content."
     *              )
     *          )
     *      )
     *  )
     * @SWG\Response(
     *     response=422,
     *     description="Unprocessable Entity",
     *     @SWG\Schema(
     *          @SWG\Property(
     *              property="reasonCode",
     *              type="string",
     *              example="1"
     *          ),
     *          @SWG\Property(
     *              property="reasonText",
     *              type="string",
     *              example="failure"
     *          ),
     *          @SWG\Property(
     *              property="error",
     *              type="object",
     *              @SWG\Property(
     *                  property="code",
     *                  type="integer",
     *                  example="1014"
     *              ),
     *              @SWG\Property(
     *                  property="text",
     *                  type="string",
     *                  example="Invalid Credentials found in Request Content."
     *              )
     *          )
     *      )
     *  )
     *  @SWG\Response(
     *     response=500,
     *     description="Internal Server Error",
     *     @SWG\Schema(
     *          @SWG\Property(
     *              property="reasonCode",
     *              type="string",
     *              example="1"
     *          ),
     *          @SWG\Property(
     *              property="reasonText",
     *              type="string",
     *              example="failure"
     *          ),
     *          @SWG\Property(
     *              property="error",
     *              type="object",
     *              @SWG\Property(
     *                  property="code",
     *                  type="integer",
     *                  example="500"
     *              ),
     *              @SWG\Property(
     *                  property="text",
     *                  type="string",
     *                  example="An error occurred on the server."
     *              )
     *          )
     *      )
     *  )
     *  @SWG\Response(
     *     response=503,
     *     description="Service Unavaliable.",
     *     @SWG\Schema(
     *          @SWG\Property(
     *              property="reasonCode",
     *              type="string",
     *              example="1"
     *          ),
     *          @SWG\Property(
     *              property="reasonText",
     *              type="string",
     *              example="failure"
     *          ),
     *          @SWG\Property(
     *              property="error",
     *              type="object",
     *              @SWG\Property(
     *                  property="code",
     *                  type="integer",
     *                  example="503"
     *              ),
     *              @SWG\Property(
     *                  property="text",
     *                  type="string",
     *                  example="Service Temporarily Unavailable"
     *              )
     *          )
     *      )
     *  )
     *
     *  @SWG\Tag(name="User")
     *  @param Request $request
     *
     *  @return array
     **/
    public function createOAuthToken(Request $request)
    {
        $logger = $this->container->get('monolog.logger.exception');
        // $response to be returned from API.
        $response = null;
        try {
            $utils = $this->container->get('eat24.utils');
            // Trimming Request Content.
            $content = $utils->trimArrayValues(json_decode(trim($request->getContent()), true));
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
     *
     * Creates new Access Token using refresh token
     *
     * @Post("/renew.{_format}")
     *
     * @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     required=true,
     *     @SWG\Schema(
     *          @SWG\Property(
     *              property="refreshToken",
     *              type="string",
     *              example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.afnnvqmfdAq"
     *          )
     *     )
     *  )
     * @SWG\Response(
     *     response=200,
     *     description="Returns back Auth token",
     *     @SWG\Schema(
     *          @SWG\Property(
     *              property="reasonCode",
     *              type="string",
     *              example="0"
     *          ),
     *          @SWG\Property(
     *              property="reasonText",
     *              type="string",
     *              example="Success"
     *          ),
     *          @SWG\Property(
     *              property="AuthenticationResponse",
     *              type="object",
     *              @SWG\Property(
     *                  property="accessToken",
     *                  type="string",
     *                  example="string"
     *              )
     *          )
     *      )
     *  )
     * @SWG\Response(
     *     response=400,
     *     description="Bad Request.",
     *     @SWG\Schema(
     *          @SWG\Property(
     *              property="reasonCode",
     *              type="string",
     *              example="1"
     *          ),
     *          @SWG\Property(
     *              property="reasonText",
     *              type="string",
     *              example="failure"
     *          ),
     *          @SWG\Property(
     *              property="error",
     *              type="object",
     *              @SWG\Property(
     *                  property="code",
     *                  type="integer",
     *                  example="1014"
     *              ),
     *              @SWG\Property(
     *                  property="text",
     *                  type="string",
     *                  example="Invalid Credentials found in Request Content."
     *              )
     *          )
     *      )
     *  )
     * @SWG\Response(
     *     response=422,
     *     description="Unprocessable Entity",
     *     @SWG\Schema(
     *          @SWG\Property(
     *              property="reasonCode",
     *              type="string",
     *              example="1"
     *          ),
     *          @SWG\Property(
     *              property="reasonText",
     *              type="string",
     *              example="failure"
     *          ),
     *          @SWG\Property(
     *              property="error",
     *              type="object",
     *              @SWG\Property(
     *                  property="code",
     *                  type="integer",
     *                  example="1021"
     *              ),
     *              @SWG\Property(
     *                  property="text",
     *                  type="string",
     *                  example="Refresh Token provided in request has been expired."
     *              )
     *          )
     *      )
     *  )
     *  @SWG\Response(
     *     response=500,
     *     description="Internal Server Error",
     *     @SWG\Schema(
     *          @SWG\Property(
     *              property="reasonCode",
     *              type="string",
     *              example="1"
     *          ),
     *          @SWG\Property(
     *              property="reasonText",
     *              type="string",
     *              example="failure"
     *          ),
     *          @SWG\Property(
     *              property="error",
     *              type="object",
     *              @SWG\Property(
     *                  property="code",
     *                  type="integer",
     *                  example="500"
     *              ),
     *              @SWG\Property(
     *                  property="text",
     *                  type="string",
     *                  example="An error occurred on the server."
     *              )
     *          )
     *      )
     *  )
     *  @SWG\Response(
     *     response=503,
     *     description="Service Unavaliable.",
     *     @SWG\Schema(
     *          @SWG\Property(
     *              property="reasonCode",
     *              type="string",
     *              example="1"
     *          ),
     *          @SWG\Property(
     *              property="reasonText",
     *              type="string",
     *              example="failure"
     *          ),
     *          @SWG\Property(
     *              property="error",
     *              type="object",
     *              @SWG\Property(
     *                  property="code",
     *                  type="integer",
     *                  example="503"
     *              ),
     *              @SWG\Property(
     *                  property="text",
     *                  type="string",
     *                  example="Service Temporarily Unavailable"
     *              )
     *          )
     *      )
     *  )
     *  @SWG\Tag(name="User")
     *
     * @Options("/renew.{_format}")
     * @param Request $request
     * @return array
     */
    public function refreshOAuthToken(Request $request)
    {
        $logger = $this->container->get('monolog.logger.exception');
        // $response to be returned from API.
        $response = null;
        try {
            $utils = $this->container->get('eat24.utils');
            // Trimming Request Content.
            $content = $utils->trimArrayValues(json_decode(trim($request->getContent()), true));

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
                        ->processOAuthRefreshRequest($content)['message']['response']
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
     * @Post("/users.{_format}")
     *
     * @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     required=true,
     *     @SWG\Schema(
     *          @SWG\Property(
     *              property="UserRequest",
     *              type="object",
     *              @SWG\Property(
     *              property="username",
     *              type="string",
     *              example="jayraj"
     *              ),
     *               @SWG\Property(
     *              property="emailId",
     *              type="string",
     *              example="jayraj.arora@gmail.com"
     *              ),
     *               @SWG\Property(
     *              property="password",
     *              type="string",
     *              example="jayraj123"
     *              ),
     *               @SWG\Property(
     *              property="phoneNumber",
     *              type="integer",
     *              example="7895980866"
     *              )
     *          )
     *     )
     *  )
     * @SWG\Response(
     *     response=200,
     *     description="Returns back Auth token",
     *     @SWG\Schema(
     *          @SWG\Property(
     *              property="reasonCode",
     *              type="string",
     *              example="0"
     *          ),
     *          @SWG\Property(
     *              property="reasonText",
     *              type="string",
     *              example="Success"
     *          ),
     *          @SWG\Property(
     *              property="AuthenticationResponse",
     *              type="object",
     *              @SWG\Property(
     *                  property="accessToken",
     *                  type="string",
     *                  example="string"
     *              )
     *          )
     *      )
     *  )
     * @SWG\Response(
     *     response=400,
     *     description="Bad Request.",
     *     @SWG\Schema(
     *          @SWG\Property(
     *              property="reasonCode",
     *              type="string",
     *              example="1"
     *          ),
     *          @SWG\Property(
     *              property="reasonText",
     *              type="string",
     *              example="failure"
     *          ),
     *          @SWG\Property(
     *              property="error",
     *              type="object",
     *              @SWG\Property(
     *                  property="code",
     *                  type="integer",
     *                  example="1014"
     *              ),
     *              @SWG\Property(
     *                  property="text",
     *                  type="string",
     *                  example="Invalid Credentials found in Request Content."
     *              )
     *          )
     *      )
     *  )
     * @SWG\Response(
     *     response=422,
     *     description="Unprocessable Entity",
     *     @SWG\Schema(
     *          @SWG\Property(
     *              property="reasonCode",
     *              type="string",
     *              example="1"
     *          ),
     *          @SWG\Property(
     *              property="reasonText",
     *              type="string",
     *              example="failure"
     *          ),
     *          @SWG\Property(
     *              property="error",
     *              type="object",
     *              @SWG\Property(
     *                  property="code",
     *                  type="integer",
     *                  example="1021"
     *              ),
     *              @SWG\Property(
     *                  property="text",
     *                  type="string",
     *                  example="Refresh Token provided in request has been expired."
     *              )
     *          )
     *      )
     *  )
     *  @SWG\Response(
     *     response=500,
     *     description="Internal Server Error",
     *     @SWG\Schema(
     *          @SWG\Property(
     *              property="reasonCode",
     *              type="string",
     *              example="1"
     *          ),
     *          @SWG\Property(
     *              property="reasonText",
     *              type="string",
     *              example="failure"
     *          ),
     *          @SWG\Property(
     *              property="error",
     *              type="object",
     *              @SWG\Property(
     *                  property="code",
     *                  type="integer",
     *                  example="500"
     *              ),
     *              @SWG\Property(
     *                  property="text",
     *                  type="string",
     *                  example="An error occurred on the server."
     *              )
     *          )
     *      )
     *  )
     *  @SWG\Response(
     *     response=503,
     *     description="Service Unavaliable.",
     *     @SWG\Schema(
     *          @SWG\Property(
     *              property="reasonCode",
     *              type="string",
     *              example="1"
     *          ),
     *          @SWG\Property(
     *              property="reasonText",
     *              type="string",
     *              example="failure"
     *          ),
     *          @SWG\Property(
     *              property="error",
     *              type="object",
     *              @SWG\Property(
     *                  property="code",
     *                  type="integer",
     *                  example="503"
     *              ),
     *              @SWG\Property(
     *                  property="text",
     *                  type="string",
     *                  example="Service Temporarily Unavailable"
     *              )
     *          )
     *      )
     *  )
     *  @SWG\Tag(name="User")
     * @Options("/users.{_format}")
     * @param Request $request
     * @return array
     */
    public function createUser(Request $request)
    {
        $logger = $this->container->get('monolog.logger.exception');
        // $response to be returned from API.
        $response = null;
        try {
            $utils = $this->container->get('eat24.utils');
            $content = json_decode(trim($request->getContent()), true);
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
     * @Put("/users.{_format}")
     * @Options("/users.{_format}")
     * @param Request $request
     * @return array
     */
    public function updateUser(Request $request)
    {
        $logger = $this->container->get('monolog.logger.exception');
        // $response to be returned from API.
        $response = null;
        try {
            $utils = $this->container->get('eat24.utils');
            $content = json_decode(trim($request->getContent()), true);
            // Trimming Request Content.
            $content = !empty($content) ? $utils->trimArrayValues($content) : $content;

            // Validating the request content.
            $validateResult = $this->container
                ->get('eat24.user_api_validate_service')
                ->validateUpdateUserRequest($content)
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
     * @Get("/profile.{_format}")
     * @Options("/profile.{_format}")
     * @return array
     */
    public function getUserDetails()
    {
        $logger = $this->container->get('monolog.logger.exception');
        // $response to be returned from API.
        $response = null;
        try {
            // Processing the request and creating the final streamed response to be sent in response.
            $profileResult = $this->container
                ->get('eat24.user_api_processing_service')
                ->processGetUserProfileRequest()
            ;
            $user = $profileResult['message']['response']['profileDetails'];
            // Creating final response Array to be released from API Controller.
            $response = $this->container
                ->get('eat24.api_response_service')
                ->createUserApiSuccessResponse(
                    'UserResponse', [
                        'phoneNumber' => $user['phoneNumber'],
                        'username' => $user['username']
                    ]
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
     * @Post("/users/addresses.{_format}")
     * @Put("/users/addresses.{_format}")
     * @Options("/address.{_format}")
     * @param Request $request
     *
     *  @return array
     **/
    public function createUpdateAddress(Request $request)
    {
        $logger = $this->container->get('monolog.logger.exception');
        // $response to be returned from API.
        $response = null;
        try {
            $utils = $this->container->get('eat24.utils');
            // Trimming Request Content.
            $content = $utils->trimArrayValues(json_decode(trim($request->getContent()), true));

            $isUpdate = Request::METHOD_PUT === $request->getMethod();
            // Validating the request content.
            $validatedResult = $this->container
                ->get('eat24.user_api_validate_service')
                ->validateAddUpdateAddressRequest($content, $isUpdate)
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

    /**
     * To get address list of user
     *
     * @Get("/users/addresses.{_format}")
     * @Options("/users/addresses.{_format}")
     * @param Request $request
     *
     *  @return array
     **/
    public function viewUserAddressList(Request $request)
    {
        $logger = $this->container->get('monolog.logger.exception');
        // $response to be returned from API.
        $response = null;
        try {
            // Processing email id and getting response.
            $result = $this->container->get('eat24.user_api_processing_service')->processListAddressRequest();

            // Creating and final array of response from API.
            $response = $this->container
                ->get('eat24.api_response_service')
                ->createUserApiSuccessResponse('AddressListResponse', $result['message']['response'])
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
     * @Delete("/users/addresses.{_format}")
     * @Options("/users/addresses.{_format}")
     * @param Request $request
     * @return array|null
     */
    public function deleteAddress(Request $request)
    {
        $logger = $this->container->get('monolog.logger.exception');
        $response = null;
        try {
            // validating the address code in the delete request
            $content = $this->container->get('eat24.utils')
                ->trimArrayValues(json_decode(trim($request->getContent()), true))
            ;
            $this->container->get('eat24.user_api_validate_service')
                ->validateDeleteAddressRequest($content)
            ;
            $this->container->get('eat24.user_api_processing_service')
                ->processDeleteAddressRequest($content)
            ;
            $response = $this->container->get('eat24.api_response_service')
                ->createUserApiSuccessResponse('DeleteAddressResponse', [
                    'status' => $this->container->get('translator')->trans('api.response.success.address_deleted')
                ]);
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
     * @Get("/users/addresses/check-is-deliverable.{_format}")
     * @Options("/users/addresses/check-is-deliverable.{_format}")
     * @param Request $request
     * @return array|null
     */
    public function checkDeliveryLocation(Request $request)
    {
        $logger = $this->container->get('monolog.logger.exception');
        $response = null;
        try {
            $content = $this->container->get('eat24.utils')
                ->trimArrayValues(json_decode(trim($request->getContent()), true))
            ;
            $validatedResult = $this->container->get('eat24.user_api_validate_service')
                ->validateCheckDeliveryLocationRequest($content)
            ;
            $result = $this->container->get('eat24.user_api_processing_service')
                ->processCheckDeliveryLocationRequest($validatedResult['response'], $content['detectLocationRequest'])
            ;
            $translationKey = $result['status'] ? 'api.response.success.location_deliverable'
                :  'api.response.success.location_not_deliverable'
            ;
            $response = $this->container->get('eat24.api_response_service')
                ->createUserApiSuccessResponse('detectLocationResponse',[
                        'status' =>  $this->container->get('translator')->trans($translationKey),
                        'isDeliverable' => $result['status']
                    ]
                )
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
