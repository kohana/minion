<?php

/**
 * Interface that all minion tasks must implement
 *
 */
abstract class Minion_Task {

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

		if($task_name === NULL)
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
}
