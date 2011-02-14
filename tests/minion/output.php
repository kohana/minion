<?php

/**
 * Testcase for Minion_Output
 *
 * @group minion
 * @group minion.core
 * @author Matt Button <matthew@sigswitch.com>
 */
class Minion_OutputTest extends Kohana_Unittest_TestCase
{
	/**
	 * Test that the instance method will return the singleton, or will 
	 * generate a new one if it does not exist
	 *
	 * @test
	 * @covers Minion_Output::instance
	 */
	public function test_instance()
	{
		$instance = Minion_Output::$_instance;

		$new_instance = Minion_Output::instance();

		$this->assertInstanceOf('Minion_Output', $new_instance);

		$this->assertSame($new_instance, Minion_Output::instance());

		Minion_Output::$_instance = $instance;
	}

	/**
	 * Test that we can add a writer to the queue
	 *
	 * @test
	 * @covers Minion_Output::add_writer
	 */
	public function test_add_writer()
	{
		$writer = $this->getMockForAbstractClass('Minion_Output_Writer');
		$output = new Minion_Output();

		$this->assertSame($output, $output->add_writer($writer));

		$this->assertAttributeSame(array($writer), '_writers', $output);
	}

	/**
	 * Test that calling write() passes the output on to all of the writers that 
	 * it contains.
	 *
	 * @test
	 * @covers Minion_Output::write
	 */
	public function test_write()
	{
		$message  = 'Damn something\'s wrong';
		$type     = Minion_Output::SUCCESS;

		$writer = $this->getMockForAbstractClass('Minion_Output_Writer');

		$writer
			->expects($this->once())
			->method('write')
			->with($message, $type);

		$output = new Minion_Output();

		$output->add_writer($writer);

		$this->assertSame($output, $output->write($message, $type));
	}
}
