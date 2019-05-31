<?php
/**
 *
 *  @category Service
 *  @author <jayraja@mindfiresolutions.com>
 */
namespace AppBundle\Service;

use AppBundle\Constants\ErrorConstants;
use AppBundle\Entity\Restaurant;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Class RestaurantApiProcessingService
 * @package AppBundle\Service
 */
class RestaurantApiProcessingService extends BaseService
{
    /**
     * Process the filter request and return back the list of restaurants back in the result
     * @param $content
     * @return mixed
     */
    public function processRestaurantFilterRequest($content)
    {
        $processResult['status'] = false;
        try {
            // check if empty
            $filter = !empty($content['filter']) ? $content['filter'] : null;
            $sort = !empty($content['sort']) ? $content['sort'] : null;

            // fetching the restaurant repository
            $restaurantRepo = $this->entityManager->getRepository('AppBundle:Restaurant');
            // setting range of restaurant to use for filtering records via geo-point
            $rangeForRestaurant = !empty($content['filter']['latitude'])
                ? $this->serviceContainer->getParameter('restaurant_range')
                : null
            ;
            // count total number of records by applying the filter(without pagination)
            $total = $restaurantRepo->countUserRecords($rangeForRestaurant, $filter, $sort);

            // filter and fetch restaurants according to the pagination set
            $restaurantListObj = $restaurantRepo->fetchRestaurantListData(
                $rangeForRestaurant,
                $filter,
                $sort,
                $content['pagination']
            );
            $host = $this->serviceContainer->get('request_stack')->getCurrentRequest()->getHost();
            // get image directory
            $imgDir = $this->serviceContainer->getParameter('image_dir');
            $restaurantList = [];
            // create restaurant list array as required
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
            // create response
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
            // fetch menu items for a particular restaurant
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
            // prepare menu api response
            $processResult['data'] = [
                'restaurantName' => $restaurant->getName(),
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
