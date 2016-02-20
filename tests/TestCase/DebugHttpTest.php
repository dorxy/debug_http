<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link      http://cakephp.org CakePHP(tm) Project
 * @since     debug_kit 2.0
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace DebugHttp\Test\TestCase;

use Cake\TestSuite\TestCase;

/**
 * DebugPanel TestCase
 */
class DebugHttpTest extends TestCase
{

    public function testTitle()
    {
        $this->assertEquals('Test', 'Test');
    }
}
