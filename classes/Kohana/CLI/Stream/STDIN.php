<?php defined('SYSPATH') OR die('No direct script access.');
/**
* STDIN interface
*
* @package   Kohana/Minion
* @category  CLI
* @author    Kohana Team
* @copyright (c) 2009-2014 Kohana Team
* @license   http://kohanaframework.org/license
*/
interface Kohana_CLI_Stream_STDIN
{
	public function options($options = NULL);
	
	public function read($text = '', array $options = NULL);
}
