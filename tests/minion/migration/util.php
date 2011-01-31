<?php

/**
 * Testcase for Minion_Migration_Util
 *
 * @group minion
 **/
class Minion_Migration_UtilTest extends Kohana_Unittest_TestCase {

	/**
	 * Provides test data for test_compile_migrations_from_files()
	 *
	 * @return array
	 */
	public function provider_compile_migrations_from_files()
	{
		return array(
			array(
				array(
					'myapp:015151051' => array('location' => 'myapp', 'description' => 'setup',        'timestamp' => '015151051', 'id' => 'myapp:015151051'),
					'myapp:015161051' => array('location' => 'myapp', 'description' => 'add-comments', 'timestamp' => '015161051', 'id' => 'myapp:015161051'),
				),
				array(
					'migrations/myapp' => array(
						'migrations/myapp/015151051_setup.php' 
							=> '/var/www/app/locations/myapp/migrations/myapp/015151051_setup.php',
						'migrations/myapp/015161051_add-comments.php' 
							=> '/var/www/app/locations/myapp/migrations/myapp/015161051_add-comments.php',
  					),
				)
			),
		);
	}

	/**
	 * Test that Minion_Migration_Util::compile_migrations_from_files accurately 
	 * compiles a set of files down into a set of migration files
	 *
	 * @test
	 * @covers Minion_Migration_Util::compile_migrations_from_files
	 * @dataProvider provider_compile_migrations_from_files
	 * @param array Expected output
	 * @param array Input Files
	 */
	public function test_compile_migrations_from_files($expected, array $files)
	{
		$this->assertSame(
			$expected, 
			Minion_Migration_Util::compile_migrations_from_files($files)
		);
	}

	/**
	 * Provides test data for test_extract_migration_info_from_filename
	 *
	 * @return array Test Data
	 */
	public function provider_get_migration_from_filename()
	{
		return array(
			array(
				array(
					'location'    => 'myapp',
					'description' => 'initial-setup',
					'timestamp'   => '1293214439',
					'id'          => 'myapp:1293214439',
				),
				'migrations/myapp/1293214439_initial-setup.php',
			),
		);
	}

	/**
	 * Tests that Minion_Migration_Util::get_migration_info_from_filename()
	 * correctly extracts information about the migration from its filename
	 *
	 * @test
	 * @covers Minion_Migration_Util::get_migration_from_filename
	 * @dataProvider provider_get_migration_from_filename
	 * @param array Expected output
	 * @param string Input filename
	 */
	public function test_get_migration_from_filename($expected, $file)
	{
		$this->assertSame(
			$expected, 
			Minion_Migration_Util::get_migration_from_filename($file)
		);
	}

	/**
	 * Provides test data for test_convert_migration_to_filename
	 *
	 * @return array Test Data
	 */
	public function provider_get_filename_from_migration()
	{
		return array(
			array(
				'myapp/1293214439_initial-setup.php',
				array(
					'location'    => 'myapp',
					'timestamp'   => '1293214439',
					'description' => 'initial-setup',
					'id'          => 'myapp:1293214439'
				),
				'myapp',
			),
		);
	}

	/**
	 * Tests that Minion_Migration_Util::get_filename_from_migration generates 
	 * accurate filenames when given a variety of migration information
	 *
	 * @test
	 * @covers Minion_Migration_Util::get_filename_from_migration
	 * @dataProvider   provider_get_filename_from_migration
	 * @param  string  Expected output
	 * @param  mixed   Migration id
	 * @param  mixed   location
	 */
	public function test_get_filename_from_migration($expected, $migration, $location)
	{
		$this->assertSame(
			$expected, 
			Minion_Migration_Util::get_filename_from_migration($migration, $location)
		);
	}

	/**
	 * Provides test data for test_get_class_from_migration
	 *
	 * @return array Test Data
	 */
	public function provider_get_class_from_migration()
	{
		return array(
			array(
				'Migration_Kohana_201012290258',
				'kohana:201012290258',
			),
			array(
				'Migration_Kohana_201012290258',
				array('location' => 'kohana', 'timestamp' => '201012290258'),
			),
		);
	}

	/**
	 * Tests that Minion_Migration_Util::get_class_from_migration can generate 
	 * a class name from information about a migration
	 *
	 * @test
	 * @covers Minion_Migration_Util::get_class_from_migration
	 * @dataProvider provider_get_class_from_migration
	 * @param string Expected output
	 * @param string|array Migration info
	 */
	public function test_get_class_from_migration($expected, $migration)
	{
		$this->assertSame(
			$expected,
			Minion_Migration_Util::get_class_from_migration($migration)
		);
	}
}
