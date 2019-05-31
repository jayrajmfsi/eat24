<?php
/**
 *  Request Listener for handling Authentication and Logging of Requests received by Application.
 *
 *  @category EventListener
 *  @author <jayraja@mindfiresolutions.com>
 */

namespace AppBundle\EventListener;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use AppBundle\Service\BaseService;

/**
 * Listeners class for logging and authenticating requests
 * Class RequestListener
 * @package AppBundle\EventListener
 */
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
     *  Function for api request authorization and logging.
     *
     *  @param GetResponseEvent $event
     *
     *  @return mixed
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        // fetching the route
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
        if (!strpos($request->getPathInfo(), '/login')
            && !strpos($request->getPathInfo(), '/renew')
            && !strpos($request->getPathInfo(), '/restaurants')
            && !strpos($request->getPathInfo(), '/restaurant/menu')
            && !($request->getPathInfo() == '/1.0/users' && $request->getMethod() == 'POST')
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
        // check if http verb is get or delete then decode the request to make it similar to a post parameters request
        if (($request->isMethod('GET') || $request->isMethod('DELETE')) && empty($content)) {

            $content = base64_decode($request->get('data'));

            $request->initialize(
                $request->query->all(),
                array(),
                $request->attributes->all(),
                $request->cookies->all(),
                array(),
                $request->server->all(),
                $content
            );
            $request->headers->set('Content-Length', strlen($content));
        }

        return $content;
    }
}
