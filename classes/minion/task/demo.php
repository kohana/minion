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
	protected $_config = array(
		'foo',
		'bar',
	);

	/**
	 * 
	 *
	 * @return 
	 */
	protected function _execute(array $params)
	{
		var_dump($params);
		echo 'foobar';
	}
}