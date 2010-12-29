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
		$db = Database::instance(Kohana::config('unittest')->db_connection);

		return new Model_Minion_Migration($db);
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
			->fetch_current_versions('location', 'id');

		$this->assertSame(
			array (
				'app'      => '1293543858_remove-password-salt-column',
				'dblogger' => '1293544858_remove-unique-index',
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
								'timestamp' => '1293543800',
								'description' => 'add-name-column-to-members',
								'location' => 'app',
								'applied' => '0',
								'id' => '1293543800_add-name-column-to-members',
							),
							array (
								'timestamp' => '1293543828',
								'description' => 'add-index-on-name',
								'location' => 'app',
								'applied' => '0',
								'id' => '1293543828_add-index-on-name',
							),
						),
					),
					'dblogger' => array(
						'direction' => true,
						'migrations' => array(
							array (
							'timestamp' => '1293544908',
							'description' => 'add-pk',
							'location' => 'dblogger',
							'applied' => '0',
							'id' => '1293544908_add-pk',
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
								'timestamp'   => '1293543858',
								'description' => 'remove-password-salt-column',
								'location'    => 'app',
								'applied'     => '1',
								'id'          => '1293543858_remove-password-salt-column'
							),
							array(
								'timestamp'   => '1293543728',
								'description' => 'create-tables',
								'location'    => 'app',
								'applied'     => '1',
								'id'          => '1293543728_create-tables',
							),
						)
					),
					'dblogger' => array(
						'direction'  => FALSE,
						'migrations' => array(
							array(
								'timestamp'   => '1293544858',
								'description' => 'remove-unique-index',
								'location'    => 'dblogger',
								'applied'     => '1',
								'id'          => '1293544858_remove-unique-index',
							),
							array(
								'timestamp'   => '1293543858',
								'description' => 'create-table',
								'location'    => 'dblogger',
								'applied'     => '1',
								'id'          => '1293543858_create-table',
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
}
