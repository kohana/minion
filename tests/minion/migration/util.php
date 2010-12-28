<?php

/**
 * Testcase for Minion_Migration_Util
 *
 * @group minion
 **/
class Minion_Migration_UtilTest extends Kohana_Unittest_TestCase {

	/**
	 * Provides test data for test_parse_migrations_from_files()
	 *
	 * @return array
	 */
	public function provider_parse_migrations_from_files()
	{
		return array(
			array(
				array(
					'015151051_setup' => array('file' => 'migrations/myapp/015151051_setup.php', 'location' => 'myapp'),
					'015161051_add-comments' => array('file' => 'migrations/myapp/015161051_add-comments.php', 'location' => 'myapp'),
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
	 * @covers Minion_Migration_Util::parse_migrations_from_files
	 * @dataProvider provider_parse_migrations_from_files
	 * @param array Expected output
	 * @param array Input Files
	 */
	public function test_parse_migrations_from_files($expected, array $files)
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
					'location'      => 'myapp',
					'id'          => '1293214439_initial-setup',
					'file'        => 'migrations/myapp/1293214439_initial-setup.php',
					'description' => 'initial-setup',
					'timestamp'   => '1293214439',
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
	 * @covers Minion_Migration_Util::extract_migration_info_from_filename
	 * @dataProvider provider_convert_file_to_migration_info
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
				'1293214439_initial-setup',
				'myapp',
			),
		);
	}

	/**
	 * Tests that Minion_Migration_Util::get_filename_from_migration generates 
	 * accurate filenames when given a variety of migration information
	 *
	 * @test
	 * @covers Minion_Migration_Util::convert_migration_to_filename
	 * @dataProvider provider_convert_migration_to_filename
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
}
