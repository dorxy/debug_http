<?php
namespace DebugHttp\Panel;

use Cake\Controller\Controller;
use Cake\Core\Configure;
use Cake\Core\StaticConfigTrait;
use Cake\Event\Event;
use Cake\Routing\Router;
use DebugKit\DebugPanel;

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
        if ( ! static::config('calls')) {
            return 0;
        }

        return count(static::config('calls'));
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
        return ['calls' => static::config('calls')];
    }

    /**
     * Add a HTTP call to the data
     *
     * @param $request
     * @param $response
     * @param $time
     */
    public static function addCall($request, $response, $time = null)
    {
        $calls   = static::config('calls');
        $calls[] = ['request' => $request, 'response' => $response, 'time' => $time];
        static::drop('calls');
        static::config('calls', $calls);
    }

    /**
     * Shutdown callback
     *
     * @param \Cake\Event\Event $event The event.
     *
     * @return void
     */
    public function shutdown(Event $event)
    {
        /**
         * @var $controller Controller;
         */
        $controller = $event->subject();
        $this->_injectScriptsAndStyles($controller->response);
    }

    /**
     * Injects the JS to build the toolbar.
     *
     * The toolbar will only be injected if the response's content type
     * contains HTML and there is a </body> tag.
     *
     * @param \Cake\Network\Response $response The response to augment.
     *
     * @return void
     */
    protected function _injectScriptsAndStyles($response)
    {
        if (strpos($response->type(), 'html') === false) {
            return;
        }
        $body = $response->body();

        //add scripts
        $pos = strrpos($body, '</body>');
        if ($pos !== false) {
            $script = '<script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/1.5.5/clipboard.min.js"></script>';
            $script .= '<script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.0.0/highlight.min.js"></script>';
            $body = substr($body, 0, $pos) . $script . substr($body, $pos);
        }

        //add styles
        $pos = strrpos($body, '</head>');
        if ($pos !== false) {
            $style = "<link rel=\"stylesheet\" type=\"text/css\" href=\"" . Router::url('/debug_http/css/requests.css') . '">';
            $style .= '<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.0.0/styles/default.min.css">';
            $body = substr($body, 0, $pos) . $style . substr($body, $pos);
        }

        $response->body($body);
    }
}
