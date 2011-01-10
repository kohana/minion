<?php

/**
 * Test for the migration model
 *
 * @group minion
 **/
class Minion_Migration_ModelTest extends Kohana_Unittest_Database_TestCase
{
	/**
	 * Gets the dataset that should be used to populate db
	 *
	 * @return PHPUnit_Extensions_Database_DataSet_IDataSet
	 */
	public function getDataSet()
	{
		return $this->createFlatXMLDataSet(
			Kohana::find_file('tests/test_data', 'minion/migration/model', 'xml')
		);
	}

	/**
	 * Get an instance of the migration model, pre-loaded with a connection to 
	 * the test database
	 *
	 * @return Model_Minion_Migration
	 */
	public function getModel()
	{
		$model = new Model_Minion_Migration($this->getKohanaConnection());

		return $model->table('test_minion_migrations');
	}

	/**
	 * Tests that the model can fetch all rows from the database
	 *
	 * @test
	 * @covers Model_Minion_Migration::fetch_all
	 */
	public function test_fetch_all()
	{
		$migrations = $this->getModel()->fetch_all();

		$this->assertSame(7, count($migrations));
	}

	/**
	 * Test that the model accurately fetches the latest versions from the database
	 *
	 * @test
	 * @covers Model_Minion_Migration::fetch_current_versions
	 */
	public function test_fetch_current_versions()
	{
		$versions = $this->getModel()
			->fetch_current_versions('location', 'timestamp');

		$this->assertSame(
			array (
				'app'      => '20101216080000',
				'dblogger' => '20101225000000',
			),
			$versions
		);
	}

	/**
	 * Provides test data for test_fetch_required_migrations
	 *
	 * @return array Test data
	 */
	public function provider_fetch_required_migrations()
	{
		return array(
			// Test a call with no params (i.e. upgrade everything to latest)
			array(
				array (
					'app' => array(
						'direction' => true,
						'migrations' => array(
							array (
								'timestamp'   => '20101215165000',
								'description' => 'add-name-column-to-members',
								'location'    => 'app',
								'applied'     => '0',
								'id'          => 'app:20101215165000'
							),
							array (
								'timestamp'   => '20101216000000',
								'description' => 'add-index-on-name',
								'location'    => 'app',
								'applied'     => '0',
								'id'          => 'app:20101216000000'
							),
						),
					),
					'dblogger' => array(
						'direction' => true,
						'migrations' => array(
							array (
							'timestamp'   => '20101226112100',
							'description' => 'add-pk',
							'location'    => 'dblogger',
							'applied'     => '0',
							'id'          => 'dblogger:20101226112100'
							),
						),
					),
				),
				NULL,
				TRUE,
				TRUE
			),
			array(
				array(
					'app' => array(
						'direction'  => FALSE,
						'migrations' => array(
							array(
								'timestamp'   => '20101216080000',
								'description' => 'remove-password-salt-column',
								'location'    => 'app',
								'applied'     => '1',
								'id'          => 'app:20101216080000'
							),
							array(
								'timestamp'   => '20101215164400',
								'description' => 'create-tables',
								'location'    => 'app',
								'applied'     => '1',
								'id'          => 'app:20101215164400'
							),
						)
					),
					'dblogger' => array(
						'direction'  => FALSE,
						'migrations' => array(
							array(
								'timestamp'   => '20101225000000',
								'description' => 'remove-unique-index',
								'location'    => 'dblogger',
								'applied'     => '1',
								'id'          => 'dblogger:20101225000000'
							),
							array(
								'timestamp'   => '20101215164500',
								'description' => 'create-table',
								'location'    => 'dblogger',
								'applied'     => '1',
								'id'          => 'dblogger:20101215164500'
							),
						)
					),
				),
				NULL,
				FALSE,
				TRUE
			),
		);
	}

	/**
	 * Tests that fetch_required_migrations() produces an accurate list of 
	 * migrations that need applying.
	 *
	 * @test
	 * @covers Model_Minion_Migration::fetch_required_migrations
	 * @dataProvider provider_fetch_required_migrations
	 * @param array             Expected output
	 * @param NULL|string|array Input Locations
	 * @param bool|string|array Input Target
	 * @param bool              Input default direction
	 */
	public function test_fetch_required_migrations($expected, $locations, $target, $default_direction)
	{
		$results = $this->getModel()
				->fetch_required_migrations($locations, $target, $default_direction);

		$this->assertSame($expected, $results);
	}

	/**
	 * Provides test data for test_get_migration
	 *
	 * @return array
	 */
	public function provider_get_migration()
	{
		return array(
			array(
				array(
					'timestamp'    => '20101215164400',
					'description'  => 'create-tables',
					'location'     => 'app',
					'applied'      => '1',
					'id'           => 'app:20101215164400'
				),
				'app',
				'20101215164400',
			)
		);
	}

	/**
	 * Tests that Model_Minion_Migration::get_migration can get a migration from 
	 * the database
	 *
	 * @test
	 * @covers Model_Minion_Migration::get_migration
	 * @dataProvider provider_get_migration
	 * @param array  Expected migration
	 * @param string The migration's location
	 * @param string The migration's timestamp
	 */
	public function test_get_migration($expected, $location, $timestamp)
	{
		$this->assertSame(
			$expected,
			$this->getModel()->get_migration($location, $timestamp)
		);
	}

	/**
	 * Provides test data for test_get_migration_throws_exception_on_invalid_input
	 *
	 * @return array
	 */
	public function provider_get_migration_throws_exception_on_invalid_input()
	{
		return array(
			array(NULL,  NULL),
			array('app', NULL),
		);
	}

	/**
	 * If invalid input is passed to get_migration then it should throw an 
	 * exception
	 *
	 * @test
	 * @covers Model_Minion_Migration::get_migration
	 * @dataProvider provider_get_migration_throws_exception_on_invalid_input
	 * @expectedException Kohana_Exception
	 */
	public function test_get_migration_throws_exception_on_invalid_input($location, $timestamp)
	{
		$this->getModel()->get_migration($location, $timestamp);
	}

	/**
	 * Provides test data for test_mark_migration
	 *
	 * @return array
	 */
	public function provider_mark_migration()
	{
		return array(
			array(
				array(
					'timestamp'   => '20101215165000',
					'description' => 'add-name-column-to-members',
					'location'    => 'app',
					'applied'     => '1',
					'id'          => 'app:20101215165000',
				),
				array(
					'timestamp'   => '20101215165000',
					'location'    => 'app',
					'description' => 'add-name-column-to-members',
				),
				TRUE
			),
			array(
				array(
					'timestamp'   => '20101215165000',
					'description' => 'add-name-column-to-members',
					'location'    => 'app',
					'applied'     => '0',
					'id'          => 'app:20101215165000',
				),
				array(
					'timestamp'   => '20101215165000',
					'location'    => 'app',
					'description' => 'add-name-column-to-members',
				),
				FALSE
			),
		);
	}

	/**
	 * Tests that Model_Minion_Migration::mark_migration() changes the applied 
	 * status of a migration
	 *
	 * @test
	 * @covers Model_Minion_Migration::mark_migration
	 * @dataProvider provider_mark_migration
	 * @param array What the DB record should look like after migration is marked
	 * @param array The migration to update
	 * @param bool  Whether the migration should be applied
	 */
	public function test_mark_migration($expected, $migration, $applied)
	{
		$model = $this->getModel();

		$model->mark_migration($migration, $applied);

		$this->assertSame(
			$expected,
			$model->get_migration($migration['location'], $migration['timestamp'])
		);
	}
}
