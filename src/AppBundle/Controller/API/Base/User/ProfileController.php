<?php
/**
 *  Controller having the CRUD actions of user profile and address
 *  @category Controller
 *  @author Jayraj Arora<jayraja@mindfiresolutions.com>
 */

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

/**
 * Contains all the user and its address related actions
 * Class ProfileController
 * @package AppBundle\Controller\API\Base\User
 */
class ProfileController extends AbstractFOSRestController
{
    /**
     * Check login credentials and create access and refresh token for user.
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
     *                      example="<emailId here>"
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
     *                  example="safaafa.dqwrava.afwqfaf"
     *              ),
     *              @SWG\Property(
     *                  property="refresToken",
     *                  type="string",
     *                  example="asdamd.adasd.asdwqqwrqqw"
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
     *                  example="1002"
     *              ),
     *              @SWG\Property(
     *                  property="text",
     *                  type="string",
     *                  example="The operation was rejected due to invalid request content provided."
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
     *                  example="Invalid Credentials provided."
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
     *  @SWG\Tag(name="Auth")
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
            $this->container->get('eat24.user_api_validate_service')->validateOAuthRequest($content);

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
     * Checks refresh token and creates new access token
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
     *                  example="1002"
     *              ),
     *              @SWG\Property(
     *                  property="text",
     *                  type="string",
     *                  example="The operation was rejected due to invalid request content provided."
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
     *  @SWG\Tag(name="Auth")
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
            $this->container->get('eat24.user_api_validate_service')->validateOAuthRefreshRequest($content);

            // Processing Request, Creating and returning final response Array from Controller.
            $response = $this->container
                ->get('eat24.api_response_service')
                ->createUserApiSuccessResponse(
                    'AuthenticationResponse',
                    $this->container->get('eat24.authenticate_authorize_service')
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
     * Create an user
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
     *     description="User created success response",
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
     *              property="UserResponse",
     *              type="object",
     *              @SWG\Property(
     *                  property="status",
     *                  type="string",
     *                  example="User was created successfully."
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
     *                  example="1002"
     *              ),
     *              @SWG\Property(
     *                  property="text",
     *                  type="string",
     *                  example="The operation was rejected due to invalid request content provided."
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
     *                  example="1013"
     *              ),
     *              @SWG\Property(
     *                  property="text",
     *                  type="string",
     *                  example="Invalid username format provided in request"
     *              )
     *          )
     *      )
     *  )
     * @SWG\Response(
     *     response=409,
     *     description="Conflict of resource",
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
     *                  example="1017"
     *              ),
     *              @SWG\Property(
     *                  property="text",
     *                  type="string",
     *                  example="Username provided is taken by someone else,please try with another username."
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
     *  @SWG\Tag(name="Profile")
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
            $this->container->get('eat24.user_api_validate_service')->validateCreateUserRequest($content);

            // Processing the request and creating the final streamed response to be sent in response.
            $this->container->get('eat24.user_api_processing_service')->processCreateUpdateUserRequest($content);

            // Creating final response Array to be released from API Controller.
            $response = $this->container
                ->get('eat24.api_response_service')
                ->createUserApiSuccessResponse(
                    'UserResponse',
                    [
                        'status' => $this->container
                            ->get('translator.default')->trans('api.response.success.user_created')
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
     * Updates an user
     * @Put("/users.{_format}")
     * @Options("/users.{_format}")
     *
     * @SWG\Parameter(
     *     name="apiKey",
     *     in="header",
     *     required=true,
     *     description="Authorization with the help of jwt access token"
     * )
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
     *              property="oldPassword",
     *              type="string",
     *              example="mindfire"
     *              ),
     *               @SWG\Property(
     *              property="password",
     *              type="string",
     *              example="jayraj123"
     *              ),
     *               @SWG\Property(
     *              property="confirmPassword",
     *              type="integer",
     *              example="jayraj123"
     *              ),
     *               @SWG\Property(
     *              property="phoneNumber",
     *              type="integer",
     *              example="789607535"
     *              )
     *          )
     *     )
     *  )
     * @SWG\Response(
     *     response=200,
     *     description="User updated success response",
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
     *              property="UserResponse",
     *              type="object",
     *              @SWG\Property(
     *                  property="status",
     *                  type="string",
     *                  example="User was updated successfully."
     *              )
     *          )
     *      )
     *  )
     * @SWG\Response(
     *     response=400,
     *     description="Bad Request",
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
     *                  example="1019"
     *              ),
     *              @SWG\Property(
     *                  property="text",
     *                  type="string",
     *                  example="Invalid New password format provided in request content."
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
     *                  example="1018"
     *              ),
     *              @SWG\Property(
     *                  property="text",
     *                  type="string",
     *                  example="Invalid Old password provided in request content."
     *              )
     *          )
     *      )
     *  )
     *  @SWG\Response(
     *     response=401,
     *     description="Unauthorized",
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
     *                  example="1016"
     *              ),
     *              @SWG\Property(
     *                  property="text",
     *                  type="string",
     *                  example="Access Token provided in request has been expired."
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
     *  @SWG\Tag(name="Profile")
     *
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
            $validateResult = $this->container->get('eat24.user_api_validate_service')
                ->validateUpdateUserRequest($content)
            ;
            $user = $validateResult['message']['response']['user'];

            // Processing the request and creating the final streamed response to be sent in response.
            $this->container->get('eat24.user_api_processing_service')->processCreateUpdateUserRequest($content, $user);

            // Creating final response Array to be released from API Controller.
            $response = $this->container
                ->get('eat24.api_response_service')
                ->createUserApiSuccessResponse(
                    'UserResponse',
                    [
                        'status' => $this->container->get('translator.default')
                            ->trans('api.response.success.user_updated')
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
     * Gets the user Profile
     * @Get("/profile.{_format}")
     * @Options("/profile.{_format}")
     *
     * @SWG\Parameter(
     *     name="apiKey",
     *     in="header",
     *     required=true,
     *     description="Authorization with the help of jwt access token"
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Returns back the user profile",
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
     *              property="UserResponse",
     *              type="object",
     *              @SWG\Property(
     *                  property="phoneNumber",
     *                  type="string",
     *                  example="78960445543"
     *              ),
     *              @SWG\Property(
     *                  property="username",
     *                  type="string",
     *                  example="jka"
     *              )
     *          )
     *      )
     *  )
     * @SWG\Response(
     *     response=401,
     *     description="Unauthorized",
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
     *                  example="1016"
     *              ),
     *              @SWG\Property(
     *                  property="text",
     *                  type="string",
     *                  example="Access Token provided in request has been expired."
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
     *  @SWG\Tag(name="Profile")
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
                    'UserResponse',
                    [
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
     * @Options("/users/addresses.{_format}")
     *
     * @SWG\Parameter(
     *     name="apiKey",
     *     in="header",
     *     required=true,
     *     description="Authorization with the help of jwt access token"
     * )
     * @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     required=true,
     *     @SWG\Schema(
     *          @SWG\Property(
     *              property="UserDeliveryAddressRequest",
     *              type="object",
     *                  @SWG\Property(
     *                      property="location",
     *                      type="string",
     *                      example="Chandaka Industrial Estat"
     *                  ),
     *                  @SWG\Property(
     *                      property="addressCode",
     *                      type="string",
     *                      example="155527114514742"
     *                  ),
     *                 @SWG\Property(
     *                      property="completeAddress",
     *                      type="string",
     *                      example="Chandaka Industrial Estate,DLF Building"
     *                  )
     *          )
     *     )
     *  )
     * @SWG\Response(
     *     response=200,
     *     description="Address updated/created success response",
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
     *              property="UserDeliveryAddressResponse",
     *              type="object",
     *              @SWG\Property(
     *                  property="status",
     *                  type="string",
     *                  example="Address was updated succesfully."
     *              ),
     *              @SWG\Property(
     *                  property="addressCode",
     *                  type="string",
     *                  example="155527114514742"
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
     *                  example="1002"
     *              ),
     *              @SWG\Property(
     *                  property="text",
     *                  type="string",
     *                  example="The operation was rejected due to invalid request content provided"
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
     *                  example="1009"
     *              ),
     *              @SWG\Property(
     *                  property="text",
     *                  type="string",
     *                  example="Invalid Geo-location point format found in request content"
     *              )
     *          )
     *      )
     *  )
     * @SWG\Response(
     *     response=401,
     *     description="Unauthorized",
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
     *                  example="1016"
     *              ),
     *              @SWG\Property(
     *                  property="text",
     *                  type="string",
     *                  example="Access Token provided in request has been expired."
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
     * @SWG\Tag(name="Address")
     *
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
            $validatedResult = $this->container->get('eat24.user_api_validate_service')
                ->validateAddUpdateAddressRequest($content, $isUpdate)
            ;
            $address = $validatedResult['address'] ? $validatedResult['address'] : null;
            // Processing Request Content and Getting Result.
            $result = $this->container->get('eat24.user_api_processing_service')
                ->processUpdateAddressRequest(
                    $content['UserDeliveryAddressRequest'],
                    $validatedResult['user'],
                    $address
                )
            ;

            $transMessageKey = (false === $isUpdate) ? 'api.response.success.address_added'
                :  'api.response.success.address_updated'
            ;
            // Creating and final array of response from API.
            $response = $this->container
                ->get('eat24.api_response_service')
                ->createUserApiSuccessResponse(
                    'UserDeliveryAddressResponse',
                    [
                        'status' => $this->container->get('translator.default')->trans($transMessageKey),
                        'addressCode' => $result['addressCode']
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

    /**
     * To get address list of user
     * @Get("/users/addresses.{_format}")
     * @Options("/users/addresses.{_format}")
     *
     * @SWG\Parameter(
     *     name="apiKey",
     *     in="header",
     *     required=true,
     *     description="Authorization with the help of jwt access token"
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Returns back lists of addresses",
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
     *              property="AddressListResponse",
     *              type="array",
     *              @SWG\Items(
     *                  @SWG\Property(
     *                      property="completeAddress",
     *                      type="string",
     *                      example="Chandaka Industrial Estate,DLF Building, UG"
     *                  ),
     *                  @SWG\Property(
     *                     property="location",
     *                     type="string",
     *                     example="Chandaka Industrial"
     *                  ),
     *                  @SWG\Property(
     *                     property="location",
     *                     type="string",
     *                     example="Chandaka Industrial"
     *                  ),
     *                  @SWG\Property(
     *                     property="addressCode",
     *                     type="string",
     *                     example="155527114514742"
     *                  ),
     *                  @SWG\Property(
     *                     property="nickName",
     *                     type="string",
     *                     example="hotel"
     *                  )
     *              )
     *          )
     *      )
     *  )
     * @SWG\Response(
     *     response=401,
     *     description="Unauthorized",
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
     *                  example="1016"
     *              ),
     *              @SWG\Property(
     *                  property="text",
     *                  type="string",
     *                  example="Access Token provided in request has been expired."
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
     *  @SWG\Tag(name="Address")
     *
     *  @return array
     **/
    public function viewUserAddressList()
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
     * Deletes an address
     *
     * @Delete("/users/addresses.{_format}")
     * @Options("/users/addresses.{_format}")
     *
     * @SWG\Parameter(
     *     name="apiKey",
     *     in="header",
     *     required=true,
     *     description="Authorization with the help of jwt access token"
     * )
     * @SWG\Parameter(
     *     name="data",
     *     in="query",
     *     description="The data is base64 encoded version of addressCode",
     *     required=true,
     *     type="string"
     *  )
     * @SWG\Response(
     *     response=200,
     *     description="Deleted address success response",
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
     *              property="DeleteAddressResponse",
     *              type="object",
     *              @SWG\Property(
     *                  property="status",
     *                  type="string",
     *                  example="Address was deleted succesfully."
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
     *                  example="1002"
     *              ),
     *              @SWG\Property(
     *                  property="text",
     *                  type="string",
     *                  example="The operation was rejected due to invalid request content provided."
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
     *                  example="1003"
     *              ),
     *              @SWG\Property(
     *                  property="text",
     *                  type="string",
     *                  example="Invalid address code provided in request."
     *              )
     *          )
     *      )
     *  )
     * @SWG\Response(
     *     response=401,
     *     description="Unauthorized",
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
     *                  example="1016"
     *              ),
     *              @SWG\Property(
     *                  property="text",
     *                  type="string",
     *                  example="Access Token provided in request has been expired."
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
     * @SWG\Tag(name="Address")
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
                ->createUserApiSuccessResponse(
                    'DeleteAddressResponse',
                    [
                        'status' => $this->container->get('translator')->trans('api.response.success.address_deleted')
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

    /**
     * Check Address is deliverable
     *
     * @Get("/users/addresses/check-is-deliverable.{_format}")
     * @Options("/users/addresses/check-is-deliverable.{_format}")
     *
     * @SWG\Parameter(
     *     name="apiKey",
     *     in="header",
     *     required=true,
     *     description="Authorization with the help of jwt access token"
     * )
     * @SWG\Parameter(
     *     name="data",
     *     in="query",
     *     description="The data is base64 encoded.",
     *     required=true,
     *     type="string"
     *  )
     * @SWG\Response(
     *     response=200,
     *     description="Check the location is deliverable and returns back appropriate response",
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
     *              property="detectLocationResponse",
     *              type="object",
     *              @SWG\Property(
     *                  property="isDeliverable",
     *                  type="boolean",
     *                  example=false
     *              ),
     *              @SWG\Property(
     *                  property="status",
     *                  type="string",
     *                  example="Sorry. The Address is not deliverable. Please try with another address or add
     *                  another address"
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
     *                  example="1002"
     *              ),
     *              @SWG\Property(
     *                  property="text",
     *                  type="string",
     *                  example="The operation was rejected due to invalid request content provided."
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
     *                  example="1004"
     *              ),
     *              @SWG\Property(
     *                  property="text",
     *                  type="string",
     *                  example="Invalid Restaurant Code found in request content"
     *              )
     *          )
     *      )
     *  )
     * @SWG\Response(
     *     response=401,
     *     description="Unauthorized",
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
     *                  example="1016"
     *              ),
     *              @SWG\Property(
     *                  property="text",
     *                  type="string",
     *                  example="Access Token provided in request has been expired."
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
     * @SWG\Tag(name="Address")
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
                ->createUserApiSuccessResponse(
                    'detectLocationResponse',
                    [
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
