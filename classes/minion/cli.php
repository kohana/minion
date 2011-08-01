<?php

class Minion_CLI extends CLI {

	public static $wait_msg = 'Press any key to continue...';

	protected static $foreground_colors = array(
		'black' => '0;30',
		'dark_gray' => '1;30',
		'blue' => '0;34',
		'light_blue' => '1;34',
		'green' => '0;32',
		'light_green' => '1;32',
		'cyan' => '0;36',
		'light_cyan' => '1;36',
		'red' => '0;31',
		'light_red' => '1;31',
		'purple' => '0;35',
		'light_purple' => '1;35',
		'brown' => '0;33',
		'yellow' => '1;33',
		'light_gray' => '0;37',
		'white' => '1;37',
	);
	protected static $background_colors = array(
		'black' => '40',
		'red' => '41',
		'green' => '42',
		'yellow' => '43',
		'blue' => '44',
		'magenta' => '45',
		'cyan' => '46',
		'light_gray' => '47',
	);

	/**
	 * Reads input from the user. This can have either 1 or 2 arguments.
	 *
	 * Usage:
	 *
	 * // Waits for any key press
	 * CLI::read();
	 *
	 * // Takes any input
	 * $color = CLI::read('What is your favorite color?');
	 *
	 * // Will only accept the options in the array
	 * $ready = CLI::read('Are you ready?', array('y','n'));
	 *
	 * @author     Fuel Development Team
	 * @license    MIT License
	 * @copyright  2010 - 2011 Fuel Development Team
	 * @link       http://fuelphp.com
	 * @return string the user input
	 */
	public static function read()
	{
		$args = func_get_args();

		// Ask question with options
		if (count($args) == 2)
		{
			list($output, $options) = $args;
		}

		// No question (probably been asked already) so just show options
		elseif (count($args) == 1 && is_array($args[0]))
		{
			$output = '';
			$options = $args[0];
		}

		// Question without options
		elseif (count($args) == 1 && is_string($args[0]))
		{
			$output = $args[0];
			$options = array();
		}

		// Run out of ideas, EPIC FAIL!
		else
		{
			$output = '';
			$options = array();
		}

		// If a question has been asked with the read
		if (!empty($output))
		{
			$options_output = '';
			if (!empty($options))
			{
				$options_output = ' [ '.implode(', ', $options).' ]';
			}

			fwrite(STDOUT, $output.$options_output.': ');
		}

		// Read the input from keyboard.
		$input = trim(fgets(STDIN));

		// If options are provided and the choice is not in the array, tell them to try again
		if (!empty($options) && !in_array($input, $options))
		{
			Minion_CLI::write('This is not a valid option. Please try again.'.PHP_EOL);

			$input = Minion_CLI::read($output, $options);
		}

		// Read the input
		return $input;
	}
	
	/**
	 * Experimental feature.
	 * 
	 * Reads hidden input from the user
	 * 
	 * Usage: 
	 * 
	 * $password = Minion_CLI::password('Enter your password : ');
	 * 
	 * @author Mathew Davies.
	 * @return string
	 */
	public static function password($text = '')
	{
		if (Kohana::$is_windows)
		{
			$vbscript = sys_get_temp_dir().'Minion_CLI_Password.vbs';
			
			// Create temporary file
			file_put_contents($vbscript, 'wscript.echo(InputBox("'.addslashes($text).'"))');
	    
	    $password = shell_exec('cscript //nologo '.escapeshellarg($command));
	    
	    // Remove temporary file.
	    unlink($vbscript);
		}
		else
		{
			$password = shell_exec('/usr/bin/env bash -c \'read -s -p "'.escapeshellcmd($text).'" var && echo $var\'');
		}
		
		Minion_CLI::write();
		
		return trim($password);
	}

	/**
	 * Outputs a string to the cli. If you send an array it will implode them
	 * with a line break.
	 *
	 * @author     Fuel Development Team
	 * @license    MIT License
	 * @copyright  2010 - 2011 Fuel Development Team
	 * @link       http://fuelphp.com
	 * @param string|array $text the text to output, or array of lines
	 */
	public static function write($text = '', $foreground = null, $background = null)
	{
		if (is_array($text))
		{
			$text = implode(PHP_EOL, $text);
		}

		if ($foreground OR $background)
		{
			$text = Minion_CLI::color($text, $foreground, $background);
		}

		fwrite(STDOUT, PHP_EOL.$text);
	}
	
	/**
	 * Outputs a string to the cli, replacing the previous line.
	 *
	 * @param string|array $text the text to output, or array of lines
	 */
	public static function write_replace($text = '', $foreground = null, $background = null)
	{
		if ($foreground OR $background)
		{
			$text = Minion_CLI::color($text, $foreground, $background);
		}

		fwrite(STDOUT, "\r\033[K".$text);
	}

	/**
	 * Waits a certain number of seconds, optionally showing a wait message and
	 * waiting for a key press.
	 *
	 * @author     Fuel Development Team
	 * @license    MIT License
	 * @copyright  2010 - 2011 Fuel Development Team
	 * @link       http://fuelphp.com
	 * @param int $seconds number of seconds
	 * @param bool $countdown show a countdown or not
	 */
	public static function wait($seconds = 0, $countdown = false)
	{
		if ($countdown === true)
		{
			$time = $seconds;

			while ($time > 0)
			{
				fwrite(STDOUT, $time.'... ');
				sleep(1);
				$time--;
			}

			Minion_CLI::write();
		}
		else
		{
			if ($seconds > 0)
			{
				sleep($seconds);
			}
			else
			{
				Minion_CLI::write(Minion_CLI::$wait_msg);
				Minion_CLI::read();
			}
		}
	}

	/**
	 * Returns the given text with the correct color codes for a foreground and
	 * optionally a background color.
	 *
	 * @author     Fuel Development Team
	 * @license    MIT License
	 * @copyright  2010 - 2011 Fuel Development Team
	 * @link       http://fuelphp.com
	 * @param string $text the text to color
	 * @param atring $foreground the foreground color
	 * @param string $background the background color
	 * @return string the color coded string
	 */
	public static function color($text, $foreground, $background = null)
	{

		if (Kohana::$is_windows)
		{
			return $text;
		}

		if (!array_key_exists($foreground, Minion_CLI::$foreground_colors))
		{
			throw new Kohana_Exception('Invalid CLI foreground color: '.$foreground);
		}

		if ($background !== null and !array_key_exists($background, Minion_CLI::$background_colors))
		{
			throw new Kohana_Exception('Invalid CLI background color: '.$background);
		}

		$string = "\033[".Minion_CLI::$foreground_colors[$foreground]."m";

		if ($background !== null)
		{
			$string .= "\033[".Minion_CLI::$background_colors[$background]."m";
		}

		$string .= $text."\033[0m";

		return $string;
	}

}
