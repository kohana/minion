<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * CLI log writer, outputs messages to screen.
 * 
 * [!!] The PHP defines a few constants for 
 * [CLI input/output streams](http://php.net/commandline.io-streams).
 * 
 * @package    Kohana/Minion
 * @category   Logging
 * @author     Kohana Team
 * @copyright  (c) 2008-2014 Kohana Team
 * @license    http://kohanaphp.com/license
 */
abstract class Kohana_Log_CLI extends Log_Writer {

	/**
	 * @var  resource  Output stream
	 */
	protected $_stream;

	/** 
	 * Sets output stream.
	 *
	 *     $writer = new Log_CLI(STDOUT);
	 *
	 * @param   resource|null  $stream  STDOUT or STDERR by default
	 * @return  void
	 */
	public function __construct($stream = NULL)
	{
		if ($stream === NULL)
		{
			$stream = STDERR;
		}
		elseif ($stream !== STDOUT AND $stream !== STDERR)
		{
			throw new Minion_Exception('Class Log_CLI: invalid stream, use STDERR/STDOUT');
		}

		// Determine the stream
		$this->_stream = $stream;
	}

	/**
	 * Writes each of the messages to output stream.
	 *
	 *     $writer->write($messages);
	 *
	 * @param   array  $messages
	 * @return  void
	 */
	public function write(array $messages)
	{
		foreach ($messages as $message)
		{
			// Writes out each message
			fwrite($this->_stream, $this->format_message($message).PHP_EOL);
		}
	}

}
