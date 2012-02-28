<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Minion valid class
 *
 * @package    Kohana
 * @category   Minion
 * @author     Kohana Team
 * @copyright  (c) 2009-2011 Kohana Team
 * @license    http://kohanaframework.org/license
 */
class Kohana_Minion_Valid
{
	/**
	 * Validates that an option is part of a task
	 *
	 * @return null
	 */
	public static function option(Validation $v, $key, Minion_Task $task)
	{
		if ( ! array_key_exists($key, $task->get_accepted_options()))
		{
			$v->error($key, 'minion_option');
		}
	}
}