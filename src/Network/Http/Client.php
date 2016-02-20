<?php
namespace DebugHttp\Network\Http;

use Cake\Core\Configure;
use DebugHttp\Panel\ClientCallPanel;
use DebugKit\DebugTimer;

/**
 * Class Client
 *
 * Client automatically registers all requests and responses with the panel
 *
 * @package DebugHttp\Network\Http
 */
class Client extends \Cake\Network\Http\Client
{

    /**
     * Helper method for doing non-GET requests.
     *
     * @param string $method  HTTP method.
     * @param string $url     URL to request.
     * @param mixed  $data    The request body.
     * @param array  $options The options to use. Contains auth, proxy etc.
     *
     * @return \Cake\Network\Http\Response
     */
    protected function _doRequest($method, $url, $data, $options)
    {
        $request = $this->_createRequest($method, $url, $data, $options);

        $timerKey = 'debug_http.call.' . $url;
        if (Configure::read('debug')) {
            DebugTimer::start($timerKey, $method . ' ' . $url);
        }

        $response = $this->send($request, $options);

        if (Configure::read('debug')) {
            DebugTimer::stop($timerKey);
            ClientCallPanel::addCall($request, $response, DebugTimer::elapsedTime($timerKey));
        }

        return $response;
    }
}
