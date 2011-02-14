<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Interface that all minion tasks must implement
 */
abstract class Minion_Task {

	/**
	 * Factory for loading minion tasks
	 *
	 * @throws Kohana_Exception
	 * @param  string The task to load
	 * @return Minion_Task The Minion task
	 */
	public static function factory($task, $output = NULL)
	{
		if (is_string($task))
		{
			$class = Minion_Util::convert_task_to_class_name($task);

			$task = new $class($output);
		}

		if ( ! $task instanceof Minion_Task)
		{
			throw new Kohana_Exception(
				"Task ':task' is not a valid minion task", 
				array(':task' => get_class($task))
			);
		}

		return $task;
	}
	/**
	 * A set of config options that the task accepts on the command line
	 * @var array
	 */
	protected $_config = array();

	/**
	 * The output writer used to display output for this task
	 * Use Minion_Task::write() to write output
	 * @var Minion_Output
	 */
	private $_output = NULL;

	/**
	 * Constructs the minion task.
	 *
	 * Accepts a minion output object as its only parameter
	 *
	 * @param Minion_Output The output system to use
	 */
	public function __construct(Minion_Output $output = NULL)
	{
		$this->_output = $output === NULL ? Minion_Output::instance() : $output;
	}

	/**
	 * Gets the task name for the task
	 * 
	 * @return string
	 */
	public function __toString()
	{
		static $task_name = NULL;

		if ($task_name === NULL)
		{
			$task_name = Minion_Util::convert_class_to_task($this);
		}

		return $task_name;
	}

	/**
	 * Get a set of config options that this task can accept
	 *
	 * @return array
	 */
	public function get_config_options()
	{
		return $this->_config;
	}

	/**
	 * Execute the task with the specified set of config
	 *
	 * @return boolean TRUE if task executed successfully, else FALSE
	 */
	abstract public function execute(array $config);

	/**
	 * Proxies output to the minion output manager
	 *
	 * @param string  The output
	 * @param integer The output type (see Minion_Output)
	 * @return Minion_Task $this
	 */
	public function write($output, $type = NULL)
	{
		$this->_output->write($output, $type);

		return $this;
	}
}
