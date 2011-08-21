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
		if ( ! Kohana::$is_cli)
		{
			throw new Kohana_Exception("Minion can only be ran from the cli");
		}

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

		$options = $task->get_config_options();

		if ( ! empty($options))
		{
			$config = call_user_func_array(array('CLI', 'options'), $options);
		}

		echo $task->execute($config);
	}
}