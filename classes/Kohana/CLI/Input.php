<?php defined('SYSPATH') OR die('No direct script access.');
/**
* Minion helper class, interact with the command line by accepting input options.
*
* @package   Kohana/Minion
* @category  Helper
* @author    Kohana Team
* @copyright (c) 2009-2014 Kohana Team
* @license   http://kohanaframework.org/license
*/
class Kohana_CLI_Input extends Kohana_CLI implements CLI_Stream_STDIN{

	/**
	 * Returns one or more command-line options. 
	 * Options are specified using standard CLI syntax:
	 *
	 *     php index.php --username=john.smith --password=secret --var="some value with spaces"
	 *
	 *     // Get the values of 'username' and 'password'
	 *     $auth = $input->options('username', 'password');
	 *
	 * @param   string  $options, ...  option name
	 * @return  mixed
	 */
	public function options($options = NULL)
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
	 *     if ($cli->read())
	 *     // Takes any input
	 *     $color = $cli->read('What is your favorite color?');
	 *     // Will only accept the options in the array
	 *     $ready = $cli->read('Are you ready?', array('y','n'));
	 *
	 * @param  string  $text    text to show user before waiting for input
	 * @param  array   $options array of options the user is shown
	 * @return string  the user input
	 */
	public function read($text = '', array $options = NULL)
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
			fwrite(STDOUT, __('Invalid option value. Please try again.'));

			$input = $this->read($text, $options);
		}

		// Read the input
		return $input;
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
	public function wait($seconds = 0, $countdown = FALSE)
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

			fwrite(STDOUT, '');
		}
		else
		{
			if ($seconds > 0)
			{
				sleep($seconds);
			}
			else
			{
				fwrite(STDOUT, __('Press any key to continue...'));
				$this->read();
			}
		}
	}	
}