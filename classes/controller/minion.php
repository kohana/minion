<?php

/**
 * Controller for interacting with minion on the cli
 *
 * @author Matt Button <matthew@sigswitch.com>
 **/
class Controller_Minion extends Controller
{
	/**
	 * Prevent Minion from being run over http
	 */
	public function before()
	{
		if( ! Kohana::$is_cli)
		{
			throw new Kohana_Exception("Minion can only be ran from the cli");
		}

		return parent::before();
	}

	/**
	 * Prints out the help for a specific task
	 *
	 */
	public function action_help()
	{
		$tasks = Minion_Util::compile_task_list(Kohana::list_files('classes/minion/task'));
		$task  = $this->request->param('task');
		$view  = NULL;

		if(empty($task))
		{
			$view = new View('minion/help/list');

			$view->tasks = $tasks;
		}
		else
		{
			$class = Minion_Util::convert_task_to_class_name($task);

			if( ! class_exists($class))
			{
				echo View::factory('minion/help/error')
					->set('error', 'Task "'.$task.'" does not exist');

				exit(1);
			}

			$inspector = new ReflectionClass($class);

			list($description, $tags) = Minion_Util::parse_doccomment($inspector->getDocComment());

			$view = View::factory('minion/help/task')
				->set('description', $description)
				->set('tags', (array) $tags)
				->set('task', $task);
		}

		echo $view;
	}

	/**
	 * Handles the request to execute a task.
	 *
	 * Responsible for parsing the tasks to execute & also any config items that 
	 * should be passed to the tasks
	 */
	public function action_execute()
	{
		$task = trim($this->request->param('task'));

		if(empty($task))
			return $this->action_help();

		try 
		{
			$task = Minion_Task::factory($task);
		}
		catch(Exception $e)
		{
			echo View::factory('minion/help/error')
				->set('error', 'Task "'.$task.'" does not exist');

			exit(1);
		}

		$options = $task->get_config_options();

		$config = call_user_func_array(array('CLI', 'options'), $options);

		echo $task->execute($config);
	}
}
