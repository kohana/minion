<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Basic minion exception class.
 *
 * @package   Kohana/Minion
 * @category  Exception
 * @author    Kohana Team
 * @copyright (c) 2009-2014 Kohana Team
 * @license   http://kohanaframework.org/license
 */
abstract class Kohana_Minion_Exception extends Kohana_Exception {

	/**
	 * Inline exception handler, displays the error message, 
	 * source of the exception, and the stack trace of the error.
	 *
	 * @param  Exception $e
	 * @return boolean
	 */
	public static function handler(Exception $e)
	{
		try
		{
			$text = ($e instanceof Minion_Exception) ? $e->_cli_format() : parent::text($e);
			fwrite(STDERR, $text);

			$exit_code = $e->getCode();

			// Never exit '0' after an exception.
			if ($exit_code == 0)
			{
				$exit_code = 1;
			}

			exit($exit_code);
		}
		catch (Exception $e)
		{
			// Display the exception text
			fwrite(STDERR, parent::text($e));

			// Exit with an error status
			exit(1);
		}
	}

	/**
	* Formating error message for display in CLI.
	*
	* @return string
	*/
	protected function _cli_format()
	{
		return parent::text($this);
	}

}
