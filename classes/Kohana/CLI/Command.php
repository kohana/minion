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
	 * Trait STDIO
	 */
	use STDIO;

	/**
	 *
	 * @var string
	 */
	protected $task_name;

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
		 * STDIO Trait
		 */
		$this->set_options($options);
		$this->set_output($output);
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
	 * Inject dependencies into Task
	 *
	 * @return Minion_Task
	 */
	protected function prepare()
	{
		// Create a new instance of the task
		$task = Minion_Task::factory($this->task_name);

		// Set CLI_Options
		$task->set_options($this->get_options());

		// Set CLI_Stream_STDOUT
		$task->set_output($this->get_output());

		// Set View Closure
		$view = function($file=NULL, $data=NULL)
		{
			return View::factory($file, $data);
		};
		$task->set_view_builder($view);

		// Set Validation Closure
		$validation = function($array = [])
		{
			return Validation::factory($array);
		};
		$task->set_validation_builder($validation);

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
