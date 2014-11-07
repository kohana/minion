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
class Kohana_CLI_Output extends Kohana_CLI implements Kohana_CLI_Stream_STDOUT {
	
	/**
	 * Outputs the body when cast to string
	 *
	 * @return string
	 */
	public function __toString()
	{
		return $this->_body;
	}
	
	/**
	 * Outputs a string to the CLI. 
	 * If you send an array it will implode them with a line break.
	 *
	 *     $cli->write($string);
	 *     
	 * @param  string|array $text the text to output or array of lines
	 * @return void
	 */
	public function write($text = '')
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
	 *     $this->write_replace('0%');
	 *     $this->write_replace('25%');
	 *     $this->write_replace('50%');
	 *     $this->write_replace('75%');
	 *     // Done writing this line
	 *     $this->write_replace('100%', TRUE);
	 *
	 * @param  string  $text     the text to output
	 * @param  boolean $end_line whether the line is done being replaced
	 * @return void
	 */
	public function write_replace($text = '', $end_line = FALSE)
	{
		// Append a newline if $end_line is TRUE
		if ($end_line === TRUE)
		{
			$text .= PHP_EOL;
		}
		fwrite(STDOUT, "\r\033[K".$text);
	}
}