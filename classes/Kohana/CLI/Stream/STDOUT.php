<?php
/**
* STDOUT interface
*
* @package   Kohana/Minion
* @category  CLI
* @author    Kohana Team
* @copyright (c) 2009-2014 Kohana Team
* @license   http://kohanaframework.org/license
*/
interface Kohana_CLI_Stream_STDOUT
{
	public function write($text = '');
}
