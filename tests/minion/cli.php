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
	 * Tests for ANSI support on Windows.
	 *
	 * @covers Minion_CLI::supports_ansi
	 */
	public function test_ansi_support_on_windows()
	{
		$ansicon = getenv('ANSICON') ? getenv('ANSICON') : FALSE;
		$is_windows = Kohana::$is_windows;

		// Fake a Windows environment
		Kohana::$is_windows = TRUE;
		putenv('ANSICON=TRUE');

		$this->assertTrue(Minion_CLI::supports_ansi());

		// Restore the environment
		Kohana::$is_windows = $is_windows;
		$ansicon = $ansicon ? 'ANSICON='.$ansicon : 'ANSICON';
		putenv($ansicon);
	}

	/**
	 * Tests color output.
	 *
	 * @dataProvider provider_color_output
	 * @covers Minion_CLI::color
	 * @param  string  $colored     The string with color codes
	 * @param  string  $text        The original string
	 * @param  string  $foreground  The foreground color
	 * @param  string  $background  The background color
	 */
	public function test_color_output($colored, $text, $foreground, $background)
	{
		try
		{
			$actual = Minion_CLI::color($text, $foreground, $background);
		}
		catch (Kohana_Exception $e)
		{
			$this->assertRegExp('/Invalid CLI (foreground|background) color/', $e->getMessage());
			return;
		}

		$expected = Minion_CLI::supports_ansi() ? $colored : $text;
		$this->assertSame($expected, $actual);
	}

	/**
	 * Provides test data for test_color_output.
	 *
	 * @return array
	 */
	public function provider_color_output()
	{
		return array(
			array("\033[0;31m\033[42mtext\033[0m", 'text', 'red', 'green'),
			array("\033[0;31mtext\033[0m", 'text', 'red', NULL),
			// Invalid colors:
			array(NULL, NULL, 'puce', 'lemon'),
			array(NULL, NULL, 'red', 'lemon'),
			array(NULL, NULL, 'puce', NULL),
			array(NULL, NULL, NULL, NULL),
		);
	}

}
