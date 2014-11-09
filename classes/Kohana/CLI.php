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
abstract class Kohana_CLI {

	/**
	 * 
	 * @param string $name
	 * @param array  $options
	 * @return \Kohana_Minion_CLI
	 * @throws Minion_Exception
	 */
	public static function factory($name, array $options = NULL)
	{
		$class = "CLI_{$name}";
		if (class_exists($class))
		{
			return new $class($options);
		}
		throw new Minion_Exception("CLI interface $class not found.");
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
	 * @param  atring $foreground the foreground color, uses [$this->$_foreground_colors]
	 * @param  string $background the background color, uses [$this->$_background_colors]
	 * @return string the color coded string
	 * @throws Minion_Exception
	 * @uses   Kohana::$is_windows
	 */
	public function color($text, $foreground, $background = NULL)
	{
		return $text;
	}

}
