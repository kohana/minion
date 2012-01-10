<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Controller for interacting with minion on the cli
 *
 * @author Matt Button <matthew@sigswitch.com>
 */
class Controller_Minion extends Kohana_Controller
{
	/**
	 * The task to be executed
	 * @var string
	 */
	protected $_task = NULL;

	/**
	 * Prevent Minion from being run over http
	 */
	public function before()
	{
		$this->_task = $this->request->param('task');

		$options = CLI::options('help', 'task');

		if (array_key_exists('help', $options))
		{
			$this->request->action('help');
		}

		if ( ! empty($options['task']))
		{
			$this->_task = $options['task'];
		}

		return parent::before();
	}

	/**
	 * Retrieves the current minion task.
	 *
	 * @return Minion_Task
	 */
	protected function _retrieve_task()
	{
		try
		{
			return Minion_Task::factory($this->_task);
		}
		catch(Exception $e)
		{
			echo View::factory('minion/help/error')
				->set('error', 'Task "'.$this->_task.'" does not exist');

			exit(1);
		}
	}

	/**
	 * Prints out the help for a specific task
	 *
	 */
	public function action_help()
	{
		$tasks = Minion_Util::compile_task_list(Kohana::list_files('classes/minion/task'));
		$view  = NULL;

		if (empty($this->_task))
		{
			$view = new View('minion/help/list');

			$view->tasks = $tasks;
		}
		else
		{
			$inspector = new ReflectionClass($this->_retrieve_task());

			list($description, $tags) = Minion_Util::parse_doccomment($inspector->getDocComment());

			$view = View::factory('minion/help/task')
				->set('description', $description)
				->set('tags', (array) $tags)
				->set('task', $this->_task);
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
		if (empty($this->_task))
		{
			return $this->action_help();
		}

		$task = $this->_retrieve_task();

		$defaults = $task->get_config_options();

		if ( ! empty($defaults))
		{
			if (Arr::is_assoc($defaults))
			{
				$options = array_keys($defaults);
				$options = call_user_func_array(array('CLI', 'options'), $options);
				$config = Arr::merge($defaults, $options);
			}
			else
			{
				// Old behavior
				$config = call_user_func_array(array('CLI', 'options'), $defaults);
			}
		}
		else
		{
			$config = array();
		}

		// Validate $config
		$validation = Validation::factory($config);
		$validation = $task->build_validation($validation);

		if ( ! $validation->check())
		{
			echo View::factory('minion/error/validation')
				->set('errors', $validation->errors($task->get_errors_file()));
		}
		else
		{
			// Finally, run the task
			echo $task->execute($config);
		}
	}
}