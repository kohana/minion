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
		return array(
			array('Task_Sitemap', 'sitemap'),
			array('Task_Db_Migrate', 'db:migrate'),
			array('Task_Db_Status', 'db:status'),
			array('', ''),
		);
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
		return array(
			array('sitemap', 'Task_Sitemap'),
			array('db:migrate', 'Task_Db_Migrate'),
		);
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
        return array(
            array(array('task' => 'help')),
            array(array('help')),
        );
    }

	/**
	 * Tests that the factory can instantiate with or without the --task CLI param
	 *
	 * @test
	 * @covers Minion_Task::factory
	 * @dataProvider provider_factory
	 * @param array Options as would be returned from Minion_CLI::options
	 */

    public function test_factory($options)
    {
        $this->assertInstanceOf('Minion_Task', Minion_Task::factory($options));
    }

}
