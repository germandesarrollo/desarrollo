<?php
/**
 * A factory class to manage the life cycle of test fixtures
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
 * @package       Cake.TestSuite.Fixture
 * @since         CakePHP(tm) v 2.0
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('ConnectionManager', 'Model');
App::uses('ClassRegistry', 'Utility');

/**
 * A factory class to manage the life cycle of test fixtures
 *
 * @package       Cake.TestSuite.Fixture
 */
class CakeFixtureManager {

/**
 * Was this class already initialized?
 *
 * @var boolean
 */
	protected $_initialized = false;

/**
 * Default datasource to use
 *
 * @var DataSource
 */
	protected $_db = null;

/**
 * Holds the fixture classes that where instantiated
 *
 * @var array
 */
	protected $_loaded = array();

/**
 * Holds the fixture classes that where instantiated indexed by class name
 *
 * @var array
 */
	protected $_fixtureMap = array();

/**
 * Inspects the test to look for unloaded fixtures and loads them
 *
 * @param CakeTestCase $test the test case to inspect
 * @return void
 */
	public function fixturize($test) {
		if (empty($test->fixtures) || !empty($this->_processed[get_class($test)])) {
			$test->db = $this->_db;
			return;
		}
		$this->_initDb();
		$test->db = $this->_db;
		if (!is_array($test->fixtures)) {
			$test->fixtures = array_map('trim', explode(',', $test->fixtures));
		}
		if (isset($test->fixtures)) {
			$this->_loadFixtures($test->fixtures);
		}

		$this->_processed[get_class($test)] = true;
	}

/**
 * Initializes this class with a DataSource object to use as default for all fixtures
 *
 * @return void
 */
	protected function _initDb() {
		if ($this->_initialized) {
			return;
		}
		$db = ConnectionManager::getDataSource('test');
		$db->cacheSources = false;
		$this->_db = $db;
		ClassRegistry::config(array('ds' => 'test'));
		$this->_initialized = true;
	}

/**
 * Looks for fixture files and instantiates the classes accordingly
 *
 * @param array $fixtures the fixture names to load using the notation {type}.{name}
 * @return void
 */
	protected function _loadFixtures($fixtures) {
		foreach ($fixtures as $index => $fixture) {
			$fixtureFile = null;
			$fixtureIndex = $fixture;
			if (isset($this->_loaded[$fixture])) {
				continue;
			}

			if (strpos($fixture, 'core.') === 0) {
				$fixture = substr($fixture, strlen('core.'));
				$fixturePaths[] = CAKE . 'Test' . DS . 'Fixture';
			} elseif (strpos($fixture, 'app.') === 0) {
				$fixture = substr($fixture, strlen('app.'));
				$fixturePaths = array(
					TESTS . 'Fixture'
				);
			} elseif (strpos($fixture, 'plugin.') === 0) {
				$parts = explode('.', $fixture, 3);
				$pluginName = $parts[1];
				$fixture = $parts[2];
				$fixturePaths = array(
					CakePlugin::path(Inflector::camelize($pluginName)) . 'Test' . DS . 'Fixture',
					TESTS . 'Fixture'
				);
			} else {
				$fixturePaths = array(
					TESTS . 'Fixture',
					CAKE  . 'Test' . DS . 'Fixture'
				);
			}

			foreach ($fixturePaths as $path) {
				$className = Inflector::camelize($fixture);
				if (is_readable($path . DS . $className . 'Fixture.php')) {
					$fixtureFile = $path . DS . $className . 'Fixture.php';
					require_once($fixtureFile);
					$fixtureClass = $className . 'Fixture';
					$this->_loaded[$fixtureIndex] = new $fixtureClass($this->_db);
					$this->_fixtureMap[$fixtureClass] = $this->_loaded[$fixtureIndex];
					break;
				}
			}
		}
	}

/**
 * Runs the drop and create commands on the fixtures if necessary.
 *
 * @param CakeTestFixture $fixture the fixture object to create
 * @param DataSource $db the datasource instance to use
 * @param boolean $drop whether drop the fixture if it is already created or not
 * @return void
 */
	protected function _setupTable($fixture, $db = null, $drop = true) {
		if (!$db) {
			$db = $this->_db;
		}
		if (!empty($fixture->created) && $fixture->created == $db->configKeyName) {
			return;
		}

		$sources = $db->listSources();
		$table = $db->config['prefix'] . $fixture->table;

		if ($drop && in_array($table, $sources)) {
			$fixture->drop($db);
			$fixture->create($db);
			$fixture->created = $db->configKeyName;
		} elseif (!in_array($table, $sources)) {
			$fixture->create($db);
			$fixture->created = $db->configKeyName;
		}
	}

/**
 * Crates the fixtures tables and inserts data on them
 *
 * @param CakeTestCase $test the test to inspect for fixture loading
 * @return void
 */
	public function load(CakeTestCase $test) {
		if (empty($test->fixtures)) {
			return;
		}
		$fixtures = $test->fixtures;
		if (empty($fixtures) || $test->autoFixtures == false) {
			return;
		}

		$test->db->begin();
		foreach ($fixtures as $f) {
			if (!empty($this->_loaded[$f])) {
				$fixture = $this->_loaded[$f];
				$this->_setupTable($fixture, $test->db, $test->dropTables);
				$fixture->insert($test->db);
			}
		}
		$test->db->commit();
	}

/**
 * Truncates the fixtures tables
 *
 * @param CakeTestCase $test the test to inspect for fixture unloading
 * @return void
 */
	public function unload(CakeTestCase $test) {
		$fixtures = !empty($test->fixtures) ? $test->fixtures : array();
		foreach (array_reverse($fixtures) as $f) {
			if (isset($this->_loaded[$f])) {
				$fixture = $this->_loaded[$f];
				if (!empty($fixture->created)) {
					$fixture->truncate($test->db);
				}
			}
		}
	}

/**
 * Truncates the fixtures tables
 *
 * @param CakeTestCase $test the test to inspect for fixture unloading
 * @return void
 * @throws UnexpectedValueException if $name is not a previously loaded class
 */
	public function loadSingle($name, $db = null) {
		$name .= 'Fixture';
		if (isset($this->_fixtureMap[$name])) {
			if (!$db) {
				$db = $this->_db;
			}
			$fixture = $this->_fixtureMap[$name];
			$this->_setupTable($fixture, $db);
			$fixture->truncate($db);
			$fixture->insert($db);
		} else {
			throw new UnexpectedValueException(__d('cake_dev', 'Referenced fixture class %s not found', $name));
		}
	}

/**
 * Drop all fixture tables loaded by this class
 *
 * @return void
 */
	public function shutDown() {
		foreach ($this->_loaded as $fixture) {
			if (!empty($fixture->created)) {
				$fixture->drop($this->_db);
			}
		}
	}
}
