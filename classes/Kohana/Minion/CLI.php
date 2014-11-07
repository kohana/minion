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
class Kohana_Minion_CLI {

	/**
	 *
	 * @var Kohana_Minion_CLI_Options
	 */
	protected static $options;

	/**
	 *
	 * @var Kohana_Minion_CLI_Input
	 */
	protected static $input;

	/**
	 *
	 * @var Kohana_Minion_CLI_Output
	 */
	protected static $output;


	/**
	 *
	 * @param array $options
	 * @return mixed
	 */
	public static function options($options = NULL)
	{
		return self::get_options()->options($options = NULL);
	}

	/**
	 *
	 * @param string $text
	 * @param array  $options
	 * @return string
	 */
	public static function read($text = '', array $options = NULL)
	{
		return self::get_input()->read($text, $options);
	}

	/**
	 *
	 * @param string $seconds
	 * @param string $countdown
	 * @return void
	 */
	public static function wait($seconds = 0, $countdown = FALSE)
	{
		return self::get_input()->wait($seconds, $countdown);
	}

	/**
	 *
	 * @param string $text
	 * @return void
	 */
	public static function write($text = '')
	{
		return self::get_output()->write($text);
	}

	/**
	 *
	 * @param string $text
	 * @param bool   $end_line
	 * @return void
	 */
	public static function write_replace($text = '', $end_line = FALSE)
	{
		return self::get_output()->write_replace($text, $end_line);
	}

	/**
	 *
	 * @param string $text
	 * @param string $foreground
	 * @param string $background
	 * @return string
	 */
	public static function color($text, $foreground, $background = NULL)
	{
		return self::get_output()->color($text, $foreground, $background);
	}
	
	
	/**
	 *
	 * @return Kohana_Minion_CLI_Options
	 */
	protected static function get_options()
	{
		return (self::$options) ?: self::$options = CLI::factory('Options');
	}

	/**
	 *
	 * @return Kohana_Minion_CLI_Input
	 */
	protected static function get_input()
	{
		return (self::$input) ?: self::$input = CLI::factory('Input');
	}

	/**
	 *
	 * @return Kohana_Minion_CLI_Output
	 */
	protected static function get_output()
	{
		return (self::$output) ?: self::$output = CLI::factory('Output');
	}	
}
