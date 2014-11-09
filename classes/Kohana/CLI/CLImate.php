<?php

use League\CLImate\CLImate;

/**
 * CLImate 
 * 
 * @link http://climate.thephpleague.com/
 */
class Kohana_CLI_CLImate extends CLImate implements CLI_Stream_STDOUT
{
	/**
	 * 
	 * @param string $text
	 */
	public function write($text = '')
	{
		$this->out($text);
	}
}
