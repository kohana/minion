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
abstract class Kohana_CLI {

	/**
	 * 
	 * @param string $name
	 * @param array  $options
	 * @return \Kohana_Minion_CLI
	 * @throws Minion_Exception
	 */
	public static function factory($name, array $options = [])
	{
		$class = "CLI_{$name}";
		if (class_exists($class))
		{
			return new $class($options);
		}
		throw new Minion_Exception("CLI interface $class not found.");
	}


	 /**
	 * @var array colour designations of the text
	 */
	protected $_foreground_colors = array(
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
	protected $_background_colors = array(
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
	 * Returns the given text with the correct color codes for a foreground 
	 * and optionally a background color.
	 *
	 * @author    Fuel Development Team
	 * @license   MIT License
	 * @copyright 2010 - 2011 Fuel Development Team
	 * @link      http://fuelphp.com
	 * 
	 * @param  string $text the text to color
	 * @param  atring $foreground the foreground color, uses [$this->$_foreground_colors]
	 * @param  string $background the background color, uses [$this->$_background_colors]
	 * @return string the color coded string
	 * @throws Minion_Exception
	 * @uses   Kohana::$is_windows
	 */
	public function color($text, $foreground, $background = NULL)
	{
		if (Kohana::$is_windows)
		{
			return $text;
		}

		if ( ! isset($this->_foreground_colors[$foreground]))
		{
			throw new Minion_Exception(
				'Invalid CLI foreground color `:color`', 
				array(':color' => $foreground)
			);
		}
		elseif ( ! empty($background) AND ! isset($this->_background_colors[$background]))
		{
			throw new Minion_Exception(
				'Invalid CLI background color `:color`', 
				array(':color' => $background)
			);
		}

		$string = "\033[".$this->_foreground_colors[$foreground]."m";

		if ( ! empty($background))
		{
			$string .= "\033[".$this->_background_colors[$background]."m";
		}

		$string .= $text."\033[0m";

		return $string;
	}

}
