<?php
/**
 * AclComponentTest file
 *
 * PHP 5
 *
 * CakePHP(tm) Tests <http://book.cakephp.org/view/1196/Testing>
 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice
 *
 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://book.cakephp.org/view/1196/Testing CakePHP(tm) Tests
 * @package       Cake.Test.Case.Controller.Component
 * @since         CakePHP(tm) v 1.2.0.5435
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
App::uses('AclComponent', 'Controller/Component');
class_exists('AclComponent');

/**
 * Test Case for AclComponent
 *
 * @package       Cake.Test.Case.Controller.Component
 */
class AclComponentTest extends CakeTestCase {
/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		if (!class_exists('MockAclImplementation', false)) {
			$this->getMock('AclInterface', array(), array(), 'MockAclImplementation');
		}
		Configure::write('Acl.classname', 'MockAclImplementation');
		$Collection = new ComponentCollection();
		$this->Acl = new AclComponent($Collection);
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		parent::tearDown();
		unset($this->Acl);
	}

/**
 * test that construtor throws an exception when Acl.classname is a
 * non-existant class
 *
 * @expectedException CakeException
 * @return void
 */
	public function testConstrutorException() {
		Configure::write('Acl.classname', 'AclClassNameThatDoesNotExist');
		$Collection = new ComponentCollection();
		$acl = new AclComponent($Collection);
	}

/**
 * test that adapter() allows control of the interal implementation AclComponent uses.
 *
 * @return void
 */
	public function testAdapter() {
		$implementation = new MockAclImplementation();
		$implementation->expects($this->once())->method('initialize')->with($this->Acl);
		$this->assertNull($this->Acl->adapter($implementation));

		$this->assertEquals($this->Acl->adapter(), $implementation, 'Returned object is different %s');
	}

/**
 * test that adapter() whines when the class is not an AclBase
 *
 * @expectedException CakeException
 * @return void
 */
	public function testAdapterException() {
		$thing = new StdClass();
		$this->Acl->adapter($thing);
	}

}
