<?php
namespace DebugClient\Panel;

use Cake\Core\Configure;
use DebugKit\DebugPanel;

/**
 * Services panel for use with DebugKit
 */
class ClientCallPanel extends DebugPanel
{
    public $plugin = 'DebugHttpClient';

    public function summary()
    {
        if ( ! isset($this->_data['calls'])) {
            return 0;
        }

        return count($this->_data['calls']);
    }
}
