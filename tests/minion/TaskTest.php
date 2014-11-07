<?php
/**
 * Test case for Minion_Util
 *
 * @package    Kohana/Minion
 * @group      kohana
 * @group      kohana.minion
 * @category   Test
 * @author     Kohana Team
 * @copyright  (c) 2009-2014 Kohana Team
 * @license    http://kohanaframework.org/license
 */
class Minion_TaskTest extends Kohana_Unittest_TestCase {

	/**
	 * Provides test data for test_convert_task_to_class_name().
	 *
	 * @return array
	 */
	public function provider_convert_task_to_class_name()
	{
		return [
			['Task_Sitemap', 'sitemap'],
			['Task_Db_Migrate', 'db:migrate'],
			['Task_Db_Status', 'db:status'],
			['', ''],
		];
	}

	/**
	 * Tests that a task can be converted to a class name.
	 *
	 * @test
	 * @covers Minion_Task::convert_task_to_class_name
	 * @dataProvider provider_convert_task_to_class_name
	 * @param string Expected class name
	 * @param string Input task name
	 */
	public function test_convert_task_to_class_name($expected, $task_name)
	{
		$this->assertSame($expected, Minion_Task::convert_task_to_class_name($task_name));
	}

	/**
	 * Provides test data for test_convert_class_to_task().
	 *
	 * @return array
	 */
	public function provider_convert_class_to_task()
	{
		return [
			['sitemap', 'Task_Sitemap'],
			['db:migrate', 'Task_Db_Migrate'],
		];
	}

	/**
	 * Tests that the task name can be found from a class name\object.
	 *
	 * @test
	 * @covers Minion_Task::convert_class_to_task
	 * @dataProvider provider_convert_class_to_task
	 * @param string Expected task name
	 * @param mixed  Input class
	 */
	public function test_convert_class_to_task($expected, $class)
	{
		$this->assertSame($expected, Minion_Task::convert_class_to_task($class));
	}

	/**
	 * Provides test data for test_factory().
	 *
	 * @return array
	 */
	public function provider_factory()
	{
		$output = CLI::factory('Output');

		return [
			['Task_Help', 'help', $output ],
			['Task_Help', 'help', NULL],
			['Task_Noop', 'noop', $output],
			['Task_Noop', 'noop', NULL],
		];
	}

	/**
	 * Tests that the factory can instantiate with or without the $output paramter
	 * and is of the expected type.
	 *
	 * @test
	 * @covers Minion_Task::factory
	 * @dataProvider provider_factory
	 * @param array Options as would be returned from Minion_CLI::options
	 */
	public function test_factory($expected, $name, $output)
	{
		$this->assertInstanceOf($expected, Minion_Task::factory($name, $output));
	}

	/**
	 * Task name is required in the factory
	 *
	 * @test
	 * @covers Minion_Task::factory
	 * @expectedException PHPUnit_Framework_Error_Warning
	 */
	public function test_factory_no_name()
	{
		Minion_Task::factory();
	}
	
	/**
	 * Testing failure with blank task name
	 *
	 * @test
	 * @covers Minion_Task::factory
	 * @expectedException Minion_Task_Exception
	 */
	public function test_factory_blank_name()
	{
		Minion_Task::factory('');
	}	

}
