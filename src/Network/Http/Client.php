<?php
namespace DebugHttp\Network\Http;

use DebugHttp\Panel\ClientCallPanel;
use DebugKit\DebugTimer;

/**
 * Class Client
 *
 * Client automatically registers all requests and responses with the panel
 *
 * @package DebugHttp\Network\Http
 */
class Client extends \Cake\Http\Client
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

        $time     = microtime();
        $timerKey = 'debug_http.call.' . $url . '.' . $time;
        if (class_exists(ClientCallPanel::class) && class_exists(DebugTimer::class)) {
            DebugTimer::start($timerKey, $method . ' ' . $url);
        }

        try {
            $response = $this->send($request, $options);
        } catch (HttpException $exception) {
            $response  = new Response(['body' => $exception->getMessage(), 'type' => 'text/plain']);
        }

        if (class_exists(ClientCallPanel::class) && class_exists(DebugTimer::class)) {
            DebugTimer::stop($timerKey);
            ClientCallPanel::addCall($request, $response, DebugTimer::elapsedTime($timerKey));
        }

        if (isset($exception)) {
            throw $exception;
        }

        return $response;
    }
}
