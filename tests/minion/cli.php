<?php
/**
 * Test case for Minion_CLI.
 *
 * @package    Kohana/Minion
 * @group      kohana
 * @group      kohana.minion
 * @category   Test
 * @author     Kohana Team
 * @copyright  (c) 2009-2012 Kohana Team
 * @license    http://kohanaframework.org/license
 */
class Minion_CLITest extends Unittest_TestCase
{
	/**
	 * Tests that only valid streams can be set.
	 *
	 * @covers Minion_CLI::set_stdout
	 * @covers Minion_CLI::set_stdin
	 */
	public function test_setting_streams()
	{
		$this->assertFalse(Minion_CLI::set_stdout('invalid'));
		$this->assertFalse(Minion_CLI::set_stdout(1));

		$stream = fopen('php://memory', 'rw');
		$this->assertTrue(Minion_CLI::set_stdout($stream));
		fclose($stream);

		$this->assertFalse(Minion_CLI::set_stdin('invalid'));
		$this->assertFalse(Minion_CLI::set_stdin(1));

		$stream = fopen('php://memory', 'rw');
		$this->assertTrue(Minion_CLI::set_stdin($stream));
		fclose($stream);
	}

	/**
	 * Tests that any output can be captured effectively.
	 *
	 * @covers Minion_CLI::write
	 */
	public function test_writing_to_and_reading_from_output_stream()
	{
		$stream = fopen('php://memory', 'rw');
		Minion_CLI::set_stdout($stream);

		$msg = 'first test message';
		Minion_CLI::write($msg);
		rewind($stream);

		$this->assertSame($msg, trim(fgets($stream)));
		rewind($stream);

		$msg = 'second test message';
		Minion_CLI::write($msg);
		rewind($stream);

		$this->assertSame($msg, trim(fgets($stream)));
		fclose($stream);
	}

	/**
	 * Tests that any input can be handled effectively.
	 *
	 * @covers Minion_CLI::read
	 */
	public function test_writing_to_and_reading_from_input_stream()
	{
		$output = fopen('php://memory', 'rw');
		$input  = fopen('php://memory', 'rw');
		$this->assertNotSame($output, $input);

		Minion_CLI::set_stdout($output);
		Minion_CLI::set_stdin($input);

		fwrite($input, 'y'.PHP_EOL.'red'.PHP_EOL);
		rewind($input);

		$read = Minion_CLI::read('Choose', array('y', 'n'));
		$this->assertSame('y', $read);

		$read = Minion_CLI::read('Choose', array('red', 'blue'));
		$this->assertSame('red', $read);

		fclose($output);
		fclose($input);
	}

	public static function tearDownAfterClass()
	{
		// Restore the default streams
		Minion_CLI::set_stdin(STDIN);
		Minion_CLI::set_stdout(STDOUT);
	}
}
