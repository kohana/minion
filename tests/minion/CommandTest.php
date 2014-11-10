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
class Minion_CommandTest extends Kohana_Unittest_TestCase {
	
	 public function setUp()
	 {
		 parent::setUp();
		 $this->markTestSkipped('Pending');
	 }

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


	public function provider_factory()
	{
		return [
			['CLI_Command', TRUE, []],
			
		];		
	}
	
	public function test_factory($expected, $task = TRUE, $params = NULL)
	{

	}
	

	public function test_construct($task, $params)
	{
		
	}

	public function test_task()
	{

	}
	
	public function test_execute()
	{

	}	
}	