<?php
/**
 * Help task to display general instructons and list all tasks.
 *
 * @package    Kohana/Minion
 * @category   Task
 * @author     Kohana Team
 * @copyright  (c) 2009-2014 Kohana Team
 * @license    http://kohanaframework.org/license
 */
abstract class Kohana_Task_Help extends Minion_Task {

	/**
	 * Generates a help list for all tasks.
	 *
	 * @return void
	 */
	protected function _execute(array $params)
	{
		// Get tasks
		$tasks = Kohana::list_files('classes/Task');
		$tasks = $this->_compile_task_list($tasks);

		// Create template with task list
		$view = View::factory('minion/help/list', array('tasks' => $tasks));

		// Render and display template
		$this->output->write($view);
	}

}
