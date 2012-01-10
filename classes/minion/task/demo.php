<?php
/**
 * 
 *
 * @package    Package
 * @author     Jeremy Bush
 * @copyright  (c) 2010 Jeremy Bush
 * @license    http://github.com/zombor/Vendo/raw/master/LICENSE
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