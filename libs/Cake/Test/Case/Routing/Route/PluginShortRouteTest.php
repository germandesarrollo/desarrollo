<?php
/**
 * CakeRequest Test case file.
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       Cake.Test.Case.Routing.Route
 * @since         CakePHP(tm) v 2.0
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('PluginShortRoute', 'Routing/Route');
App::uses('Router', 'Routing');
/**
 * test case for PluginShortRoute
 *
 * @package       Cake.Test.Case.Routing.Route
 */
class PluginShortRouteTestCase extends  CakeTestCase {
/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		Configure::write('Routing', array('admin' => null, 'prefixes' => array()));
		Router::reload();
	}

/**
 * test the parsing of routes.
 *
 * @return void
 */
	public function testParsing() {
		$route = new PluginShortRoute('/:plugin', array('action' => 'index'), array('plugin' => 'foo|bar'));

		$result = $route->parse('/foo');
		$this->assertEquals($result['plugin'], 'foo');
		$this->assertEquals($result['controller'], 'foo');
		$this->assertEquals($result['action'], 'index');

		$result = $route->parse('/wrong');
		$this->assertFalse($result, 'Wrong plugin name matched %s');
	}

/**
 * test the reverse routing of the plugin shortcut urls.
 *
 * @return void
 */
	public function testMatch() {
		$route = new PluginShortRoute('/:plugin', array('action' => 'index'), array('plugin' => 'foo|bar'));

		$result = $route->match(array('plugin' => 'foo', 'controller' => 'posts', 'action' => 'index'));
		$this->assertFalse($result, 'plugin controller mismatch was converted. %s');

		$result = $route->match(array('plugin' => 'foo', 'controller' => 'foo', 'action' => 'index'));
		$this->assertEquals($result, '/foo');
	}
}
