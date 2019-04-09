<?php

namespace AppBundle\Service;

use AppBundle\Constants\ErrorConstants;
use AppBundle\Entity\Restaurant;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class RestaurantApiValidatingService extends BaseService
{
    public function validateFilterRestaurantRequest($requestContent)
    {
        $validateResult['status'] = false;
        try {
            $content = [];
            if (empty($requestContent['RestaurantDetailsRequest'])) {
                return $validateResult;
            }

            $requestContent = $requestContent['RestaurantDetailsRequest'];
            // Validating the filters Key Values.
            if (
                !empty($requestContent['filter']['restaurantName'])
                &&  strlen($requestContent['filter']['restaurantName']) <= 100
            ) {
                $content['filter']['restaurantName'] = $requestContent['filter']['restaurantName'];
            }
            if (!empty($requestContent['filter']['longitude']) && is_float($requestContent['filter']['longitude'])
                &&  !empty($requestContent['filter']['latitude'] && $requestContent['filter']['latitude'])
            ) {
                $content['filter']['longitude'] = $requestContent['filter']['longitude'];
                $content['filter']['latitude'] = $requestContent['filter']['latitude'];
            }

            if (!empty($requestContent['filter']['cuisine'])) {
                $content['filter']['cuisine'] = $requestContent['filter']['cuisine'];
            }
            // Validating the Sort attributes.
            if (
                !empty($requestContent['sort'])
                &&  2 === count($requestContent['sort'])
                &&  isset(Restaurant::$allowedSortingAttributesMap[$requestContent['sort'][0]])
            ) {
                $content['sort'][] = $requestContent['sort'][0];
                $content['sort'][] = ('ASC' === $requestContent['sort'][1] || 'DESC' === $requestContent['sort'][1])
                    ? $requestContent['sort'][1]
                    : 'ASC'
                ;
            }

            $content['pagination'] = $this->validatePaginationArray($requestContent['pagination']);

            if (!empty($content)) {
                $validateResult['message']['response'] = [
                    'content' => $content
                ];
            }

            $validateResult['status'] = true;

        } catch (BadRequestHttpException $ex) {
            throw $ex;
        } catch (\Exception $ex) {
            $this->logger->error(__FUNCTION__.' Function failed due to Error :'. $ex->getMessage());
            throw new HttpException(500, ErrorConstants::INTERNAL_ERR);
        }

        return $validateResult;
    }

    /**
     *  Function to validate the pagination array of API content.
     *
     *  @param array $pagination
     *
     *  @return array
     */
    public function validatePaginationArray($pagination)
    {
        $validateResult = [];
        // Validating the pagination parameters.
        $validateResult['page'] = (empty($pagination['pagination']['page'])
            || !ctype_digit($pagination['pagination']['page'])
            || $pagination['pagination']['page'] < 1)
            ? 1
            : $pagination['pagination']['page']
        ;

        $validateResult['limit'] = (empty($pagination['pagination']['limit'])
            || !ctype_digit($pagination['pagination']['limit'])
            || $pagination['pagination']['limit'] < 1)
            ? 10
            : $pagination['pagination']['limit']
        ;

        return $validateResult;
    }
}