<?php
/**
* Minion helper class, interact with the command line by accepting input options.
*
* @package   Kohana/Minion
* @category  Helper
* @author    Kohana Team
* @copyright (c) 2009-2014 Kohana Team
* @license   http://kohanaframework.org/license
*/
abstract class Kohana_Minion_CLI {

	/**
	 * @var array colour designations of the text
	 */
	protected static $_foreground_colors = array(
		'black'        => '0;30',
		'dark_gray'    => '1;30',
		'blue'         => '0;34',
		'light_blue'   => '1;34',
		'green'        => '0;32',
		'light_green'  => '1;32',
		'cyan'         => '0;36',
		'light_cyan'   => '1;36',
		'red'          => '0;31',
		'light_red'    => '1;31',
		'purple'       => '0;35',
		'light_purple' => '1;35',
		'brown'        => '0;33',
		'yellow'       => '1;33',
		'light_gray'   => '0;37',
		'white'        => '1;37',
	);

	/**
	* @var array colour designations of the background
	*/
	protected static $_background_colors = array(
		'black'      => '40',
		'red'        => '41',
		'green'      => '42',
		'yellow'     => '43',
		'blue'       => '44',
		'magenta'    => '45',
		'cyan'       => '46',
		'light_gray' => '47',
	);

	/**
	 * Returns one or more command-line options. 
	 * Options are specified using standard CLI syntax:
	 *
	 *     php index.php --username=john.smith --password=secret --var="some value with spaces"
	 *
	 *     // Get the values of 'username' and 'password'
	 *     $auth = Minion_CLI::options('username', 'password');
	 *
	 * @param   string  $options, ...  option name
	 * @return  mixed
	 */
	public static function options($options = NULL)
	{
		// Get all of the requested options
		$options = func_get_args();

		// Found option values
		$values = array();

		// Skip the first option, it is always the file executed
		for ($i = 1; $i < $_SERVER['argc']; $i++)
		{
			if ( ! isset($_SERVER['argv'][$i]))
			{
				// No more args left
				break;
			}

			// Get the option
			$opt = $_SERVER['argv'][$i];

			if (substr($opt, 0, 2) !== '--')
			{
				// This is a positional argument
				$values[] = $opt;
				continue;
			}

			// Remove the '--' prefix
			$opt = substr($opt, 2);

			if (strpos($opt, '='))
			{
				// Separate the name and value
				list ($opt, $value) = explode('=', $opt, 2);
			}
			else
			{
				$value = NULL;
			}

			$values[$opt] = $value;
		}

		if ($options)
		{
			foreach ($values as $opt => $value)
			{
				if ( ! in_array($opt, $options))
				{
					// Set the given value
					unset($values[$opt]);
				}
			}
		}

		return count($options) == 1 ? array_pop($values) : $values;
	}

	/**
	 * Reads input from the user. 
	 * This can have either 1 or 2 arguments.
	 *
	 *     // Waits for any key press
	 *     if (Minion_CLI::read())
	 *     // Takes any input
	 *     $color = Minion_CLI::read('What is your favorite color?');
	 *     // Will only accept the options in the array
	 *     $ready = Minion_CLI::read('Are you ready?', array('y','n'));
	 *
	 * @param  string  $text    text to show user before waiting for input
	 * @param  array   $options array of options the user is shown
	 * @return string  the user input
	 */
	public static function read($text = '', array $options = NULL)
	{
		// If a question has been asked with the read
		$options_output = '';
		if ( ! empty($options))
		{
			$options_output = ' [ '.implode(', ', $options).' ]';
		}

		fwrite(STDOUT, $text.$options_output.': ');

		// Read the input from keyboard.
		$input = trim(fgets(STDIN));

		// If options are provided and the choice is not in the array, tell them to try again
		if ( ! empty($options) AND ! in_array($input, $options))
		{
			Minion_CLI::write(__('Invalid option value. Please try again.'));

			$input = Minion_CLI::read($text, $options);
		}

		// Read the input
		return $input;
	}

	/**
	 * Outputs a string to the CLI. 
	 * If you send an array it will implode them with a line break.
	 *
	 *     Minion_CLI::write($string);
	 *     
	 * @param  string|array $text the text to output or array of lines
	 * @return void
	 */
	public static function write($text = '')
	{
		if (is_array($text))
		{
			$text = implode(PHP_EOL, $text);
		}

		fwrite(STDOUT, $text);
	}

	/**
	 * Outputs a replacable line to the CLI. You can continue replacing the
	 * line until `TRUE` is passed as the second parameter in order to indicate
	 * you are done modifying the line.
	 *
	 *     // Sample progress indicator
	 *     Minion_CLI::write_replace('0%');
	 *     Minion_CLI::write_replace('25%');
	 *     Minion_CLI::write_replace('50%');
	 *     Minion_CLI::write_replace('75%');
	 *     // Done writing this line
	 *     Minion_CLI::write_replace('100%', TRUE);
	 *
	 * @param  string  $text     the text to output
	 * @param  boolean $end_line whether the line is done being replaced
	 * @return void
	 */
	public static function write_replace($text = '', $end_line = FALSE)
	{
		// Append a newline if $end_line is TRUE
		if ($end_line === TRUE)
		{
			$text .= PHP_EOL;
		}
		fwrite(STDOUT, "\r\033[K".$text);
	}

	/**
	 * Waits a certain number of seconds, optionally showing a wait message
	 * and waiting for a key press.
	 *
	 * @author    Fuel Development Team
	 * @license   MIT License
	 * @copyright 2010 - 2011 Fuel Development Team
	 * @link      http://fuelphp.com
	 * 
	 * @param  integer $seconds   number of seconds
	 * @param  boolean $countdown show a countdown or not
	 * @return void
	 */
	public static function wait($seconds = 0, $countdown = FALSE)
	{
		if ($countdown === TRUE)
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
				Minion_CLI::write(__('Press any key to continue...'));
				Minion_CLI::read();
			}
		}
	}

	/**
	 * Returns the given text with the correct color codes for a foreground 
	 * and optionally a background color.
	 *
	 * @author    Fuel Development Team
	 * @license   MIT License
	 * @copyright 2010 - 2011 Fuel Development Team
	 * @link      http://fuelphp.com
	 * 
	 * @param  string $text the text to color
	 * @param  atring $foreground the foreground color, uses [Minion_CLI::$_foreground_colors]
	 * @param  string $background the background color, uses [Minion_CLI::$_background_colors]
	 * @return string the color coded string
	 * @throws Minion_Exception
	 * @uses   Kohana::$is_windows
	 */
	public static function color($text, $foreground, $background = NULL)
	{
		if (Kohana::$is_windows)
		{
			return $text;
		}

		if ( ! isset(Minion_CLI::$_foreground_colors[$foreground]))
		{
			throw new Minion_Exception(
				'Invalid CLI foreground color `:color`', 
				array(':color' => $foreground)
			);
		}
		elseif ( ! empty($background) AND ! isset(Minion_CLI::$_background_colors[$background]))
		{
			throw new Minion_Exception(
				'Invalid CLI background color `:color`', 
				array(':color' => $background)
			);
		}

		$string = "\033[".Minion_CLI::$_foreground_colors[$foreground]."m";

		if ( ! empty($background))
		{
			$string .= "\033[".Minion_CLI::$_background_colors[$background]."m";
		}

		$string .= $text."\033[0m";

		return $string;
	}

}
