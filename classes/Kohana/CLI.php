<?php
/**
 * CLI factory. Used to build I/O streams
 *
 * // STDOUT
 * $output  = CLI::factory('Output');
 *
 * // Souped-up colors and formatting
 * $output  = CLI::factory('CLImate');
 *
 * // STDIN
 * $options = CLI::factory('Input');
 *
 * // Command line parameters
 * $options = CLI::factory('Options');
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
}
