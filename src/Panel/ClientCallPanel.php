<?php
namespace DebugHttp\Panel;

use Cake\Core\Configure;
use Cake\Core\StaticConfigTrait;
use DebugKit\DebugPanel;

/**
 * Services panel for use with DebugKit
 */
class ClientCallPanel extends DebugPanel
{
    use StaticConfigTrait;

    public $plugin = 'DebugHttp';

    public function summary()
    {
        if ( ! static::config('calls')) {
            return 0;
        }

        return count(static::config('calls'));
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
}
