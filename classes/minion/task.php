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
	public static function factory($task)
	{
		if ( ! is_string($task))
		{
			throw new InvalidArgumentException;
		}

		$class = Minion_Util::convert_task_to_class_name($task);

		if ( ! in_array('Minion_Task', class_parents($class)))
		{
			throw new Kohana_Exception(
				"Task ':task' is not a valid minion task",
				array(':task' => get_class($task))
			);
		}

		return new $class;
	}

	/**
	 * A set of config options that the task accepts on the command line
	 * @var array
	 */
	protected $_config = array();

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
		return (array) $this->_config;
	}

	/**
	 * Execute the task with the specified set of config
	 *
	 * @return boolean TRUE if task executed successfully, else FALSE
	 */
	abstract public function execute(array $config);
}
