<?php
/**
 *  ResponseListener for Handling the operations before releasing Response From Application.
 *
 *  @category EventListener
 *  @author <jayraja@mindfiresolutions.com>
 */

namespace AppBundle\EventListener;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use AppBundle\Service\BaseService;

/**
 * Class ResponseListener
 * @package AppBundle\EventListener
 */
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

        // can be used in case for setting CORS
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set('Access-Control-Allow-Methods', 'POST, GET, OPTIONS, DELETE, PUT');
        $response->headers->set(
            'Access-Control-Allow-Headers',
            'Origin, X-Requested-With, Content-Type, Accept, apiKey'
        );
        $response->headers->set('Access-Control-Allow-Credentials', 'true');

        $mainLogData = [
            'host' => $request->getSchemeAndHttpHost(),
            'method' => $request->getMethod()
        ];

        // Logging only the response of API requests.
        if (0 === strpos($routeName, 'api_v')) {
            return true;
        }
        // request data
        $mainLogData['request'] = [
            'headers' => $request->headers->all(),
            'content' => json_decode($request->getContent(), true)
        ];
        // response data
        $mainLogData['response'] = [
            'headers' => $response->headers->all(),
            'content' => json_decode($response->getContent(), true)
        ];
        // log the api request-response
        $this->apiLogger->debug($routeName, $mainLogData);
    }
}
