<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Demo task. Will delete this.
 *
 * @package    Kohana
 * @category   Helpers
 * @author     Kohana Team
 * @copyright  (c) 2009-2011 Kohana Team
 * @license    http://kohanaframework.org/license
 */
class Minion_Task_Demo extends Minion_Task
{
	/**
	 * 
	 *
	 * @return 
	 */
	public function execute()
	{
		var_dump($this->_options);
		echo 'foobar';
	}
}