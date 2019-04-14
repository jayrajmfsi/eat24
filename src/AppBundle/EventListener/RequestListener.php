<?php
/**
 *  Request Listener for handling Authentication and Logging of Requests received by Application.
 *
 *  @category EventListener
 */

namespace AppBundle\EventListener;

use Doctrine\DBAL\Types\Type;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use AppBundle\Service\BaseService;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class RequestListener extends BaseService
{
    /**
     * @var LoggerInterface
     */
    private $apiLogger;

    /**
     *  RequestListener constructor.
     *
     *  @param LoggerInterface $apiLogger
     */
    public function __construct(LoggerInterface $apiLogger)
    {
        $this->apiLogger = $apiLogger;
    }

    /**
     *  Function for api request authorization.
     *
     *  @param GetResponseEvent $event
     *
     *  @return boolean
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        // Checking the route hit by request is topps API route or not.
        $route = $request->attributes->get('_route');

        $this->setRequestContent($request);

        // Checking if request is not for APIs.
        if (false === strpos($route, 'api_v')) {
            return true;
        }

        // Logging request.
        $this->apiLogger->debug('API Request: ', [
            'Request' => [
                'headers' => $request->headers->all(),
                'content' => $request->getContent()
            ]
        ]);

        // Checking if Request Method is OPTIONS.
        if (Request::METHOD_OPTIONS === $request->getMethod()) {
            $event->setResponse(new JsonResponse(['status' => true]));
            return true;
        }

        // Getting Auth Service.
        $authService = $this->serviceContainer->get('eat24.authenticate_authorize_service');

        // authentication of a particular user
        if (!strpos($request->getPathInfo(), '/oauth')
            && !strpos($request->getPathInfo(), '/create')
            && !strpos($request->getPathInfo(), '/restaurant/list')
            && !strpos($request->getPathInfo(), '/restaurant/menu')
        ) {
            $authResult = $authService->authenticateApiRequest($request);
            $request->attributes->set('emailId', $authResult['message']['emailId']);
        }

        return true;
    }

    /**
     * Function to format request content.
     *
     * @param Request $request
     *
     * @return bool|string
     */
    private function setRequestContent(Request $request)
    {
        $content = $request->getContent();

        if (($request->isMethod('GET') || $request->isMethod('DELETE')) && empty($content)) {
            $content = base64_decode($request->get('data'));
            $request->initialize($request->query->all(), array(), $request->attributes->all(),
                $request->cookies->all(), array(), $request->server->all(), $content);
            $request->headers->set('Content-Length', strlen($content));
        }

        return $content;
    }
}