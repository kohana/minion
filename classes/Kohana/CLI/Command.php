<?php
/**
 * Command. Uses the [CLI_Options] class to determine what
 * [Task] to execute. Output routed to [CLI_Stream_STDOUT].
 *
 * @package    Kohana
 * @category   Base
 * @author     Kohana Team
 * @copyright  (c) 2008-2012 Kohana Team
 * @license    http://kohanaframework.org/license
 */
class Kohana_CLI_Command {

	/**
	 * Traits
	 */
	use STDIO, Builders;

	/**
	 * Constructs a CLI_Command with the given task name and params.
	 *
	 * @param string            $task
	 * @param CLI_Options       $params
	 * @param CLI_Stream_STDOUT $output
	 * @return CLI_Command
	 */
	public static function factory($task = TRUE, CLI_Options $options, CLI_Stream_STDOUT $output)
	{
		// Autodetect task name?
		if ($task===TRUE)
		{
			$task = $options->task();
		}

		return new CLI_Command($task, $options, $output);
	}

	/**
	 *
	 * @param string            $task
	 * @param CLI_Options       $options
	 * @param CLI_Stream_STDOUT $output
	 */
	public function __construct($task, CLI_Options $options, CLI_Stream_STDOUT $output)
	{
		$this->task_name($task);

		/**
		 * STDIO trait
		 */
		$this->set_options($options);
		$this->set_output($output);

		/**
		 * Builders trait
		 */
		$this->load_builders();
	}

	/**
	 * Gets and sets the resolved Task name
	 *
	 * @return string
	 */
	public function task_name($name = NULL)
	{
		if ($name === NULL)
		{
			return $this->task_name;
		}
		return $this->task_name = $name;
	}

	/**
	 * Loads builders from config file
	 */
	protected function load_builders()
	{
		$this->builders = require DOCROOT.'vendor/kohana/minion/config/builders.php';
	}

	/**
	 * Inject dependencies into Task
	 *
	 * @return Minion_Task
	 */
	protected function prepare()
	{
		// Create a new instance of the task
		$task = $this->call_builder('task', [$this->task_name]);

		// Set View Closure
		$task->set_builder('view', $this->builders['view']);

		// Set Validation Closure
		$task->set_builder('validation', $this->builders['validation']);

		// Set CLI_Options
		$task->set_options($this->options);

		// Set CLI_Stream_STDOUT
		$task->set_output($this->output);

		return $task;
	}

	/**
	 * Executes the CLI_Command by calling finding and executing a Task.
	 * Unix exit status is collected from the execution.
	 *
	 * @return int
	 */
	public function execute()
	{
		$params = $this->options->params();

		// Run the task's execute() method
		return $this->prepare()->execute($params);
	}
}
