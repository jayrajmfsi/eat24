<?php

namespace AppBundle\Controller\API\Base\Restaurant;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Options;

class RestaurantController extends AbstractFOSRestController
{
    /**
     * Fetch the restaurants list within a certain location
     *
     * @Post("/list.{_format}")
     * @Options("/list.{_format}")
     *
     * @param Request $request
     * @return array
     */
    public function fetchRestaurantList(Request $request)
    {

    }
}