<?php

/**
 * Test case for the Minion Master
 *
 * @group minion
 **/
class Minion_MasterTest extends Kohana_Unittest_TestCase
{
	/**
	 * Tests that Minion_Master::load() will accept an instance of Minion_Task 
	 * as a task
	 *
	 * @test
	 * @covers Minion_Master::load
	 */
	public function test_load_accepts_objects_as_valid_tasks()
	{
		$master = new Minion_Master;
		$task   = $this->getMockForAbstractClass('Minion_Task');

		$this->assertSame($master, $master->load($task));

		$this->assertAttributeContains($task, '_tasks', $master);
	}
}
