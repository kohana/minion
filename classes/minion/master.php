<?php

/**
 * The Minion Master is responsible for loading and executing the various minion 
 * tasks requested by the user
 *
 * @author Matt Button <matthew@sigswitch.com>
 */
class Minion_Master {

	/**
	 * Tasks the master will execute
	 * @var array
	 */
	protected $_tasks = array();

	/**
	 * Get a list of config options that the loaded tasks accept at execution
	 *
	 * @return array
	 */
	public function get_config_options()
	{
		$config = array();
		
		foreach($this->_tasks as $task)
		{
			$config[(string) $task] = (array) $task->get_config_options();
		}

		return $config;
	}

	/**
	 * Loads a number of tasks into the task master
	 * 
	 * Passed task can either be an instance of Minion_Task, a task name (e.g. 
	 * db:migrate) or an array of the above
	 *
	 * If an invalid task is passed then a Kohana_Exception will be thrown
	 *
	 * @chainable
	 * @throws  Kohana_Exception
	 * @param   array|string|Minion_Task The task(s) to load
	 * @returns Minion_Master            Chainable instance
	 */
	public function load($task)
	{
		if(is_array($task))
		{
			array_map(array($this, 'load'), $task);

			return $this;
		}

		if(is_string($task))
		{
			$class = Minion_Util::convert_task_to_class_name($task);

			$task = new $class;
		}

		if( ! $task instanceof Minion_Task)
		{
			throw new Kohana_Exception(
				"Task ':task' is not a valid minion task", 
				array(':task' => get_class($task))
			);
		}

		$this->_tasks[(string) $task] = $task;

		return $this;
	}

	/**
	 * Executes the loaded tasks one at a time
	 *
	 * @return Minion_Master Chainable instance
	 */
	public function execute(array $config = array())
	{
		if(empty($this->_tasks))
			return $this;

		foreach($this->_tasks as $task)
		{
			$task->execute(Arr::get($config, (string) $task, array()));
		}

		return $this;
	}
}
