<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Controller for interacting with minion on the cli
 *
 * @author Matt Button <matthew@sigswitch.com>
 */
class Controller_Minion extends Controller
{
	/**
	 * The task to be executed 
	 * @var string
	 */
	protected $_task = NULL;

	/**
	 * The output manager
	 * @var Minion_Output
	 */
	protected $_output = NULL;

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

		$this->_output = Minion_Output::instance()
			->add_writer(new Minion_Output_Writer_CLI());

		return parent::before();
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
			$class = Minion_Util::convert_task_to_class_name($this->_task);

			if ( ! class_exists($class))
			{
				$this->_output->write('Task "'.$this->_task.'" does not exist', Minion_Output::ERROR);
				
				exit(1);
			}

			$inspector = new ReflectionClass($class);

			list($description, $tags) = Minion_Util::parse_doccomment($inspector->getDocComment());

			$view = View::factory('minion/help/task')
				->set('description', $description)
				->set('tags', (array) $tags)
				->set('task', $this->_task);
		}

		$this->_output->write($view);
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

		try 
		{
			$task = Minion_Task::factory($this->_task, $this->_output);
		}
		catch(Exception $e)
		{
			$this->_output->write('Task "'.$this->_task.'" does not exist', Minion_Output::ERROR);

			exit(1);
		}

		$config  = array();
		$options = (array) $task->get_config_options();

		if ( ! empty($options))
		{
			$options = $task->get_config_options();
			$config  = call_user_func_array(array('CLI', 'options'), $options);
		}

		$task->execute($config);
	}
}
