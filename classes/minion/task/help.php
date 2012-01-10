<?php
/**
 * 
 *
 * @package    Package
 * @author     Jeremy Bush
 * @copyright  (c) 2010 Jeremy Bush
 * @license    http://github.com/zombor/Vendo/raw/master/LICENSE
 */
class Minion_Task_Help extends Minion_Task
{
	/**
	 * Generates a help list for all tasks
	 *
	 * @return null
	 */
	public function execute()
	{
		$tasks = Minion_Util::compile_task_list(Kohana::list_files('classes/minion/task'));

		$view = new View('minion/help/list');

		$view->tasks = $tasks;

		echo $view;
	}
}