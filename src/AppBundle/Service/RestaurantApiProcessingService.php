<?php

namespace AppBundle\Service;

use AppBundle\Constants\ErrorConstants;
use AppBundle\Entity\Restaurant;
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
            $total = $restaurantRepo->countUserRecords($rangeForRestaurant, $filter, $sort);
            $restaurantListObj = $restaurantRepo->fetchRestaurantListData(
                $rangeForRestaurant,
                $filter,
                $sort,
                $content['pagination']
            );
            $host = $this->serviceContainer->get('request_stack')->getCurrentRequest()->getHost();
            $imgDir = $this->serviceContainer->getParameter('image_dir');
            $restaurantList = [];
            foreach ($restaurantListObj as $index => $restaurant) {
                $cuisines = $restaurant->getCuisines();
                $restaurantList[$index]['name'] = $restaurant->getName();
                $restaurantList[$index]['cost'] = $restaurant->getCost();
                $restaurantList[$index]['rating'] = $restaurant->getRating();
                $restaurantList[$index]['code'] = $restaurant->getReference();
                $restaurantList[$index]['imageUrl'] = 'http://'.$host.$imgDir.$restaurant->getImageFileName();
                foreach ($cuisines as $cuisine) {
                    $restaurantList[$index]['cuisines'][] = $cuisine->getName();
                }
            }
            $processResult['data'] = [
                'restaurants' => $restaurantList,
                'count' => $total
            ];
            $processResult['status'] = true;
        } catch (\Exception $ex) {
            $this->logger->error(__FUNCTION__.' Function failed due to Error :'. $ex->getMessage());
            throw new HttpException(500, ErrorConstants::INTERNAL_ERR);
        }

        return $processResult;
    }

    /**
     * Process menu item for the particular restaurant
     * @param Restaurant $restaurant
     * @return array
     */
    public function processMenuListRequest($restaurant)
    {
        $processResult['status'] = false;
        try {
            $menuItems = $this->entityManager->getRepository('AppBundle:InRestaurant')
                ->fetchMenuItems($restaurant->getId())
            ;
            $menuItemResult = [];
            // for each category set the menu item in it
            foreach ($menuItems as $menuItem) {
                $category = $menuItem['categoryName'];
                unset($menuItem['categoryName']);
                $menuItemResult[$category][] = $menuItem;
            }
            $processResult['data'] = [
                'restaurantCode' => $restaurant->getReference(),
                'menuItems' => $menuItemResult
            ];
            $processResult['status'] = true;
        } catch (\Exception $ex) {
            $this->logger->error(__FUNCTION__.' Function failed due to Error :'. $ex->getMessage());
            throw new HttpException(500, ErrorConstants::INTERNAL_ERR);
        }

        return $processResult;
    }
}