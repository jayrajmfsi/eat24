<?php
/**
 *  Used for showing details of restaurant and its menu
 *
 *  @category Controller
 *  @author Jayraj Arora<jayraja@mindfiresolutions.com>
 */

namespace AppBundle\Controller\API\Base\Restaurant;

use AppBundle\Constants\ErrorConstants;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Options;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Swagger\Annotations as SWG;

/**
 * Restaurant Class containing all restaurant related actions
 *
 * Class RestaurantController
 * @package AppBundle\Controller\API\Base\Restaurant
 */
class RestaurantController extends AbstractFOSRestController
{
    /**
     * Fetch the restaurants list with the filters applied
     *
     * @Get("/restaurants.{_format}")
     *
     * @SWG\Parameter(
     *     name="data",
     *     in="query",
     *     description="The data is base64 encoded version of restaurant Filters",
     *     required=true,
     *     type="string"
     *  )
     *
     *  @SWG\Response(
     *     response=200,
     *     description="Lists all the restaurant according to the filters applied",
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
     *              property="RestaurantDetailsResponse",
     *              type="object",
     *              @SWG\Property(
     *                  property="count",
     *                  type="integer",
     *                  example=7
     *              ),
     *              @SWG\Property(
     *                  property="restaurants",
     *                  type="array",
     *                  @SWG\Items(
     *                      @SWG\Property(
     *                          property="cost",
     *                          type="integer",
     *                          example=200
     *                      ),
     *                      @SWG\Property(
     *                          property="rating",
     *                          type="string",
     *                          example="4.3"
     *                      ),
     *                      @SWG\Property(
     *                          property="name",
     *                          type="string",
     *                          example="marwaari"
     *                      ),
     *                      @SWG\Property(
     *                          property="code",
     *                          type="string",
     *                          example="1554964198599682"
     *                      ),
     *                      @SWG\Property(
     *                          property="imageUrl",
     *                          type="string",
     *                          example="img-1554931009.jpg"
     *                      ),
     *                      @SWG\Property(
     *                          property="cuisines",
     *                          type="array",
     *                          @SWG\Items(
     *                              type="string",
     *                              example="Indian"
     *                          )
     *                      )
     *                  )
     *              )
     *          )
     *      )
     *  )
     *
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
     * @Options("/restaurants.{_format}")
     * @SWG\Tag(name="Restaurant")
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
            $content = $utils->trimArrayValues(json_decode(trim($request->getContent()), true));
            // Validating the request content.
            $validationResult = $this->container->get('eat24.restaurant_api_validate_service')
                ->parseFilterRestaurantRequest($content)
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
                ->createUserApiSuccessResponse('RestaurantDetailsResponse', $result['data'])
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
     * Fetch the menu for a particular restaurant
     *
     * @Get("/restaurants/menu.{_format}")
     * @Options("/restaurants/menu.{_format}")
     *
     * @SWG\Parameter(
     *     name="data",
     *     in="query",
     *     description="The data is base64 encoded version of restaurantCode",
     *     required=true,
     *     type="string"
     *  )
     *
     *  @SWG\Response(
     *     response=200,
     *     description="Lists all the restaurant according to the filters applied",
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
     *              property="RestaurantDetailsResponse",
     *              type="object",
     *              @SWG\Property(
     *                  property="restaurantName",
     *                  type="string",
     *                  example="marwaari"
     *              ),
     *              @SWG\Property(
     *                  property="restaurantCode",
     *                  type="string",
     *                  example="1554964198599682"
     *              ),
     *              @SWG\Property(
     *                  property="menuItems",
     *                  type="object",
     *                  @SWG\Property(
     *                      property="Starters",
     *                      type="array",
     *                      @SWG\Items(
     *                          @SWG\Property(
     *                              property="price",
     *                              type="string",
     *                              example="120.00"
     *                          ),
     *                          @SWG\Property(
     *                              property="description",
     *                              type="string",
     *                              example="Sprinkled with red pepper over it"
     *                          ),
     *                          @SWG\Property(
     *                              property="isVeg",
     *                              type="boolean",
     *                              example=true
     *                          ),
     *                          @SWG\Property(
     *                              property="name",
     *                              type="string",
     *                              example="Veg Manchurian"
     *                          ),
     *                          @SWG\Property(
     *                              property="code",
     *                              type="string",
     *                              example="1555259317"
     *                          )
     *                      )
     *                  )
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
     * @SWG\Tag(name="Restaurant")
     * @param Request $request
     * @return array
     */
    public function showRestaurantMenu(Request $request)
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
            // Creating final response array to be released from API Controller.
            $response = $this->container->get('eat24.api_response_service')
                ->createUserApiSuccessResponse(
                    'RestaurantDetailsResponse',
                    $result['data']
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
}
