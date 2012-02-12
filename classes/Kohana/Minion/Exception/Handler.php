<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Handles CLI exceptions
 *
 * @package    Kohana
 * @category   Minion
 * @author     Kohana Team
 * @copyright  (c) 2009-2011 Kohana Team
 * @license    http://kohanaframework.org/license
 */
class Kohana_Minion_Exception_Handler extends Kohana_Kohana_Exception {

	/**
	 * Inline exception handler, displays the error message, source of the
	 * exception, and the stack trace of the error.
	 *
	 * Should this display a stack trace? It's useful.
	 *
	 * Should this still log? Maybe not as useful since we'll see the error on the screen.
	 *
	 * @uses    Kohana_Exception::text
	 * @param   Exception   $e
	 * @return  boolean
	 */
	public static function handler(Exception $e)
	{
		try
		{
			echo Kohana_Exception::text($e);
			exit($e->getCode());
		}
		catch (Exception $e)
		{
			// Clean the output buffer if one exists
			ob_get_level() and ob_clean();

			// Display the exception text
			echo Kohana_Exception::text($e), "\n";

			// Exit with an error status
			exit(1);
		}
	}
}
