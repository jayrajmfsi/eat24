<?php
/**
 *  Used for listing and placing orders
 *  @category Service
 *  @author Jayraj Arora<jayraja@mindfiresolutions.com>
 */
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
use Swagger\Annotations as SWG;

/**
 * Contains actions related to order
 * Class OrderController
 * @package AppBundle\Controller\API\Base\User
 */
class OrderController extends AbstractFOSRestController
{
    /**
     * Place an order for a particular user
     *
     * @Post("/users/orders.{_format}")
     * @Options("/users/orders.{_format}")
     *
     * @SWG\Parameter(
     *     name="apiKey",
     *     in="header",
     *     type="string",
     *     required=true,
     *     description="Authorization with the help of jwt access token"
     * )
     * @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     required=true,
     *     @SWG\Schema(
     *          @SWG\Property(
     *              property="orderRequest",
     *              type="object",
     *                  @SWG\Property(
     *                      property="restaurantCode",
     *                      type="string",
     *                      example="1554964198599682"
     *                  ),
     *                  @SWG\Property(
     *                      property="addressCode",
     *                      type="string",
     *                      example="1555076185488228"
     *                  ),
     *                  @SWG\Property(
     *                      property="totalPrice",
     *                      type="string",
     *                      example="123.90"
     *                  ),
     *                  @SWG\Property(
     *                      property="orderItems",
     *                      type="array",
     *                      @SWG\Items(
     *                          @SWG\Property(
     *                              property="name",
     *                              type="string",
     *                              example="Veg Manchurian"
     *                          ),
     *                          @SWG\Property(
     *                              property="quantity",
     *                              type="string",
     *                              example="3"
     *                          ),
     *                          @SWG\Property(
     *                              property="code",
     *                              type="string",
     *                              example="1555259317"
     *                          )
     *                      )
     *                  )
     *          )
     *     )
     *  )
     * @SWG\Response(
     *     response=200,
     *     description="Order created success response",
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
     *              property="OrderDetailsResponse",
     *              type="object",
     *              @SWG\Property(
     *                  property="transactionId",
     *                  type="string",
     *                  example="155550220099032"
     *              ),
     *              @SWG\Property(
     *                  property="status",
     *                  type="string",
     *                  example="Order Booked successfully."
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
     * @SWG\Tag(name="Order")
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
            // trimming the request content
            $utils = $this->container->get('eat24.utils');
            $content = $utils->trimArrayValues(json_decode(trim($request->getContent()), true));
            // Validating the request content.
            $validationResult = $this->container->get('eat24.user_api_validate_service')
                ->validateCreateOrderRequest($content)
            ;
            // process the create order request
            $result = $this->container->get('eat24.user_api_processing_service')
                ->processCreateOrderRequest($validationResult['message']['response'], $content['orderRequest'])
            ;
            // Creating final response Array to be released from API Controller.
            $response = $this->container->get('eat24.api_response_service')
                ->createUserApiSuccessResponse(
                    'OrderDetailsResponse',
                    [
                        'status' => $this->container->get('translator')->trans('api.response.success.order_booked'),
                        'transactionId' => $result['ref']
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
     * List orders for a particular User
     * @Get("/users/orders.{_format}")
     *
     * @SWG\Parameter(
     *     name="apiKey",
     *     in="header",
     *     type="string",
     *     required=true,
     *     description="Authorization with the help of jwt access token"
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Lists all the orders for a particular user",
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
     *              property="OrderListResponse",
     *              type="object",
     *              @SWG\Property(
     *                  property="restaurantName",
     *                  type="string",
     *                  example="marwaari"
     *              ),
     *              @SWG\Property(
     *                  property="bookedAt",
     *                  type="string",
     *                  example="2019-04-14 18:01:09"
     *              ),
     *              @SWG\Property(
     *                  property="deliveryAddress",
     *                  type="string",
     *                  example="Chandaka Industrial"
     *              ),
     *              @SWG\Property(
     *                  property="finalPrice",
     *                  type="string",
     *                  example="123.90"
     *              ),
     *              @SWG\Property(
     *                  property="menuItems",
     *                  type="array",
     *                  @SWG\Items(
     *                      @SWG\Property(
     *                          property="quantity",
     *                          type="integer",
     *                          example="3"
     *                      ),
     *                      @SWG\Property(
     *                          property="price",
     *                          type="string",
     *                          example="123.90"
     *                      ),
     *                      @SWG\Property(
     *                          property="name",
     *                          type="string",
     *                          example="Veg Manchurian"
     *                      )
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
     * @SWG\Tag(name="Order")
     * @return mixed
     */
    public function listOrders()
    {
        // response to be returned
        $response = null;
        $logger = $this->container->get('monolog.logger.exception');
        try {
            // fetch order lists
            $result = $this->container->get('eat24.user_api_processing_service')->processListOrdersRequest();

            // create order list response
            $response = $this->container->get('eat24.api_response_service')
                ->createUserApiSuccessResponse('OrderListResponse', $result['message']['response'])
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
