<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Noop task
 *
 * @package    Kohana/Minion
 * @category   Task
 * @author     Kohana Team
 * @copyright  (c) 2009-2014 Kohana Team
 * @license    http://kohanaframework.org/license
 */
abstract class Kohana_Task_Noop extends Minion_Task {
	
	protected $_options = [
	    'foo'  => 'bar',
	    'opt' => NULL,
	];

	/**
	 * Example Task.
	 *
	 * 
	 * @return void
	 */
	protected function _execute(array $params)
	{
		$out = var_export($params, TRUE);
		
		$out = $this->output->color($out,'yellow', 'green');
		
		$this->output->write("$out\n\n");
	}
}
