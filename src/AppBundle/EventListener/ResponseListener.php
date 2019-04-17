<?php
/**
 *  ResponseListener for Handling the operations before releasing Response From Application.
 *
 *  @category EventListener
 */

namespace AppBundle\EventListener;


use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use AppBundle\Service\BaseService;

class ResponseListener extends BaseService
{
    /**
     * @var LoggerInterface
     */
    private $apiLogger;

    /**
     * ResponseListener constructor.
     *
     * @param LoggerInterface $apiLogger
     */
    public function __construct(LoggerInterface $apiLogger)
    {
        $this->apiLogger = $apiLogger;
    }

    /**
     * Function to be executed before releasing final Response.
     *
     * @param FilterResponseEvent $event
     * @return mixed
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        //log request and response
        $request = $event->getRequest();
        $response = $event->getResponse();
        $routeName = $request->attributes->get('_route');

//        $response->headers->set('Access-Control-Allow-Origin', '*');
//        $response->headers->set('Access-Control-Allow-Methods', 'POST, GET, OPTIONS, DELETE, PUT');
//        $response->headers->set('Access-Control-Allow-Headers', 'Origin, X-Requested-With, Content-Type, Accept, apiKey');
//        $response->headers->set('Access-Control-Allow-Credentials', 'true');

        $mainLogData = [
            'host' => $request->getSchemeAndHttpHost(),
            'method' => $request->getMethod()
        ];

        // Logging the response of API requests.
        if (0 === strpos($routeName, 'api_v')) {
            return true;
        }
        $mainLogData['request'] = [
            'headers' => $request->headers->all(),
            'content' => json_decode($request->getContent(), TRUE)
        ];
        $mainLogData['response'] = [
            'headers' => $response->headers->all(),
            'content' => json_decode($response->getContent(), TRUE)
        ];
        $this->apiLogger->debug($routeName, $mainLogData);
    }
}