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
					'015151051_setup' => array('file' => 'migrations/myapp/015151051_setup.php', 'module' => 'myapp'),
					'015161051_add-comments' => array('file' => 'migrations/myapp/015161051_add-comments.php', 'module' => 'myapp'),
				),
				array(
					'migrations/myapp' => array(
						'migrations/myapp/015151051_setup.php' 
							=> '/var/www/app/modules/myapp/migrations/myapp/015151051_setup.php',
						'migrations/myapp/015161051_add-comments.php' 
							=> '/var/www/app/modules/myapp/migrations/myapp/015161051_add-comments.php',
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
	public function provider_extract_migration_info_from_filename()
	{
		return array(
			array(
				array(
					'module'      => 'myapp',
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
	 * Tests that Minion_Migration_Util::extract_migration_info_from_filename()
	 * correctly extracts information about the migration from its filename
	 *
	 * @test
	 * @covers Minion_Migration_Util::extract_migration_info_from_filename
	 * @dataProvider provider_extract_migration_info_from_filename
	 * @param array Expected output
	 * @param string Input filename
	 */
	public function test_extract_migration_info_from_filename($expected, $file)
	{
		$this->assertSame(
			$expected, 
			Minion_Migration_Util::extract_migration_info_from_filename($file)
		);
	}

	/**
	 * Provides test data for test_convert_migration_to_filename
	 *
	 * @return array Test Data
	 */
	public function provider_convert_migration_to_filename()
	{
		return array(
			array(
				'migrations/myapp/1293214439_initial-setup.php',
				'1293214439_initial-setup',
				'myapp',
			),
		);
	}

	/**
	 * Tests that Minion_Migration_Util::convert_migration_to_filename generates 
	 * accurate filenames when given a variety of migration information
	 *
	 * @test
	 * @covers Minion_Migration_Util::convert_migration_to_filename
	 * @dataProvider provider_convert_migration_to_filename
	 * @param  string  Expected output
	 * @param  mixed   Migration id
	 * @param  mixed   Module
	 */
	public function test_convert_migration_to_filename($expected, $migration, $module)
	{
		$this->assertSame(
			$expected, 
			Minion_Migration_Util::convert_migration_to_filename($migration, $module)
		);
	}
}
