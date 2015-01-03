<?php
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
			$stream = $message['level'] < Log::NOTICE ? STDERR : STDOUT;
			// Writes out each message
			fwrite($stream, $this->format_message($message).PHP_EOL);
		}
	}

}
