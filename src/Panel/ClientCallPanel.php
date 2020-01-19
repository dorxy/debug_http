<?php

namespace DebugHttp\Panel;

use Cake\Controller\Controller;
use Cake\Core\StaticConfigTrait;
use Cake\Error\Debugger;
use Cake\Event\EventInterface;
use Cake\Http\Response;
use Cake\Routing\Router;
use DebugKit\Controller\RequestsController;
use DebugKit\DebugPanel;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Services panel for use with DebugKit
 */
class ClientCallPanel extends DebugPanel
{
    use StaticConfigTrait;

    public $plugin = 'DebugHttp';

    /**
     * Retrieve summary, count of client calls
     *
     * @return int
     */
    public function summary()
    {
        if (!static::getConfig('calls')) {
            return 0;
        }

        return count(static::getConfig('calls'));
    }

    /**
     * Get title for use at the debug panel
     *
     * @return string
     */
    public function title()
    {
        return 'Client Calls';
    }

    /**
     * Get the panel data
     */
    public function data()
    {
        return ['calls' => static::getConfig('calls') ?: []];
    }

    /**
     * Add a HTTP call to the data
     *
     * @param RequestInterface  $request  Call request
     * @param ResponseInterface $response Call response
     * @param float             $time     duration of the call
     */
    public static function addCall(RequestInterface $request, ResponseInterface $response, $time = null)
    {
        $calls   = static::getConfig('calls');
        $trace   = Debugger::trace(['start' => 2]);
        $calls[] = [
            'request'  => [
                'uri'          => (string)$request->getUri(),
                'body'         => (string)$request->getBody(),
                'method'       => $request->getMethod(),
                'headers'      => $request->getHeaders(),
                'content-type' => $request->getHeader('Content-Type'),
            ],
            'response' => [
                'body'         => (string)$response->getBody(),
                'status_code'  => $response->getStatusCode(),
                'headers'      => $response->getHeaders(),
                'content-type' => $response->getHeader('Content-Type'),
            ],
            'time'     => $time,
            'trace'    => $trace,
        ];
        static::drop('calls');
        static::setConfig('calls', $calls);
    }

    /**
     * Shutdown callback
     *
     * @param \Cake\Event\EventInterface $event The event.
     *
     * @return void
     */
    public function shutdown(EventInterface $event)
    {
        /**
         * @var $controller Controller;
         */
        $controller = $event->getSubject();
        if ($controller instanceof RequestsController) {
            $controller->setResponse($this->_injectScriptsAndStyles($controller->getResponse()));
        }
    }

    /**
     * Injects the JS to build the toolbar and return the new response.
     *
     * The toolbar will only be injected if the response's content type
     * contains HTML and there is a </body> tag.
     *
     * @param \Cake\Http\Response $response The response to augment.
     *
     * @return \Cake\Http\Response
     */
    protected function _injectScriptsAndStyles(Response $response): Response
    {
        if (strpos($response->getType(), 'html') === false) {
            return $response;
        }
        $body = $response->getBody();

        //add scripts
        $pos = strrpos($body, '</body>');
        if ($pos !== false) {
            $script = '<script src="' . Router::url('/debug_http/js/highlight.min.js') . '"></script>';
            $script .= '<script src="' . Router::url('/debug_http/js/clipboard.min.js') . '"></script>';
            $body   = substr($body, 0, $pos) . $script . substr($body, $pos);
        }

        //add styles
        $pos = strrpos($body, '</head>');
        if ($pos !== false) {
            $style = '<link rel="stylesheet" type="text/css" href="' . Router::url('/debug_http/css/requests.css') . '">';
            $style .= '<link rel="stylesheet" type="text/css" href="' . Router::url('/debug_http/css/highlight.min.css') . '">';
            $body  = substr($body, 0, $pos) . $style . substr($body, $pos);
        }

        return $response->withStringBody($body);
    }
}
