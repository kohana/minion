<?php

/**
 * Test case for Minion_Util
 *
 * @group minion
 * @group minion.core
 */
class Minion_UtilTest extends Kohana_Unittest_TestCase
{
	/**
	 * Provides test data for test_parse_doccoment()
	 *
	 * @return array Test data
	 */
	public function provider_parse_doccoment()
	{
		return array(
			array(
				array(
					"This is my comment from something or\nother",
					array(
						'author' => 'Matt Button <matthew@sigswitch.com>',
					),
				),
				"	/**\n	 * This is my comment from something or\n	 * other\n	 * \n	 * @author Matt Button <matthew@sigswitch.com>\n	 */",
			),
		);
	}

	/**
	 * Tests Minion_Util::prase_doccoment
	 *
	 * @test
	 * @dataProvider provider_parse_doccoment
	 * @covers Minion_Util::parse_doccomment
	 * @param array Expected output
	 * @param string Input doccoment
	 */
	public function test_parse_doccoment($expected, $doccomment)
	{
		$this->assertSame($expected, Minion_Util::parse_doccomment($doccomment));
	}

	/**
	 * Provides test data for test_compile_task_list()
	 *
	 * @return array Test data
	 */
	public function provider_compile_task_list()
	{
		return array(
			array(
				array(
					'db:migrate',
					'db:status',
				),
				array (
					'classes/minion/task/db' => array (
						'classes/minion/task/db/migrate.php' => '/var/www/memberful/memberful-core/modules/kohana-minion/classes/minion/task/db/migrate.php',
						'classes/minion/task/db/status.php' => '/var/www/memberful/memberful-core/modules/kohana-minion/classes/minion/task/db/status.php',
					),
				),
			),
		);
	}

	/**
	 * Tests that compile_task_list accurately creates a list of tasks from a directory structure
	 *
	 * @test
	 * @covers Minion_Util::compile_task_list
	 * @dataProvider provider_compile_task_list
	 * @param array  Expected output
	 * @param array  List of files
	 * @param string Prefix to use
	 * @param string Separator to use
	 */
	public function test_compile_task_list($expected, $files, $prefix = '', $separator = ':')
	{
		$this->assertSame($expected, Minion_Util::compile_task_list($files, $prefix, $separator));
	}

	/**
	 * Provides test data for test_convert_task_to_class_name()
	 *
	 * @return array
	 */
	public function provider_convert_task_to_class_name()
	{
		return array(
			array('Minion_Task_Db_Migrate', 'db:migrate'),
			array('Minion_Task_Db_Status',  'db:status'),
			array('', ''),
		);
	}

	/**
	 * Tests that a task can be converted to a class name
	 *
	 * @test
	 * @covers Minion_Util::convert_task_to_class_name
	 * @dataProvider provider_convert_task_to_class_name
	 * @param string Expected class name
	 * @param string Input task name
	 */
	public function test_convert_task_to_class_name($expected, $task_name)
	{
		$this->assertSame($expected, Minion_Util::convert_task_to_class_name($task_name));
	}

	/**
	 * Provides test data for test_convert_class_to_task()
	 *
	 * @return array
	 */
	public function provider_convert_class_to_task()
	{
		return array(
			array('db:migrate', 'Minion_Task_Db_Migrate'),
		);
	}

	/**
	 * Tests that the task name can be found from a class name / object
	 *
	 * @test
	 * @covers Minion_Util::convert_class_to_task
	 * @dataProvider provider_convert_class_to_task
	 * @param string Expected task name
	 * @param mixed  Input class
	 */
	public function test_convert_class_to_task($expected, $class)
	{
		$this->assertSame($expected, Minion_Util::convert_class_to_task($class));
	}
}
