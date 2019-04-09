<?php

namespace AppBundle\Service;

use AppBundle\Constants\ErrorConstants;
use Symfony\Component\HttpKernel\Exception\HttpException;

class RestaurantApiProcessingService extends BaseService
{
    public function processRestaurantFilterRequest($content)
    {
        $processResult['status'] = false;
        try {

            $filter = !empty($content['filter']) ? $content['filter'] : null;
            $sort = !empty($content['sort']) ? $content['sort'] : null;

            $restaurantRepo = $this->entityManager->getRepository('AppBundle:Restaurant');
            $rangeForRestaurant = !empty($content['filter']['latitude'])
                ? $this->serviceContainer->getParameter('restaurant_range')
                : null
            ;
            $restaurantListObj = $restaurantRepo->fetchRestaurantListData(
                $filter,
                $sort,
                $content['pagination'],
                $rangeForRestaurant
            );

            foreach ($restaurantListObj as $index => $restaurant) {
                $cuisines = $restaurant->getCuisines();
                $restaurantList[$index]['name'] = $restaurant->getName();
                $restaurantList[$index]['cost'] = $restaurant->getCost();
                $restaurantList[$index]['rating'] = $restaurant->getCost();
                $restaurantList[$index]['code'] = $restaurant->getId();
                foreach ($cuisines as $cuisine) {
                    $restaurantList[$index]['cuisines'][] = $cuisine->getName();
                }
            }
            $processResult['data'] = [
                'restaurants' => $restaurantList,
                'count' => count($restaurantList)
            ];

            $processResult['status'] = true;
        } catch (\Exception $ex) {
            $this->logger->error(__FUNCTION__.' Function failed due to Error :'. $ex->getMessage());
            throw new HttpException(500, ErrorConstants::INTERNAL_ERR);
        }

        return $processResult;
    }
}