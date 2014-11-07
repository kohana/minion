<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Command Client. Processes a [Command]. Will echo any output and 
 * return the exit status of the command unless an unexpected error occurs.
 *
 * @package    Kohana
 * @category   Base
 * @author     Kohana Team
 * @copyright  (c) 2008-2012 Kohana Team
 * @license    http://kohanaframework.org/license
 * @since      3.1.0
 */
abstract class Kohana_CLI_Command_Client {
	
	/**
	 * Creates a new `Command_Client` object,
	 * allows for dependency injection.
	 *
	 * @param   array    $params Params
	 */
	public function __construct(array $params = [])
	{
		$this->params = $params;
	}

	/**
	 * Processes the command, executing the task that handles this
	 * command, determined by [Options].
	 *
	 *
	 *     $command->execute();
	 *
	 * @param   CLI_Command $command
	 * @return  Exit status code
	 * @throws  Kohana_Exception
	 * @uses    [Kohana::$profiling]
	 * @uses    [Profiler]
	 */
	public function execute(CLI_Command $command)
	{
		$output = CLI::factory('Output');

		return $this->execute_command($command, $output);
	}
	
	/**
	 * Processes the command passed to it and returns the exit status
	 *
	 * This method must be implemented by all clients.
	 *
	 * @param   CLI_Command   $command  Command to execute by client
	 * @param   CLI_Output    $output
	 * @return  Exit status code
	 * @since   3.4.0
	 */
	public function execute_command(CLI_Command $command, CLI_Output $output)
	{
		// Create a new instance of the task
		$task = Minion_Task::factory($command->task(), $output);
		
		// Run the task's execute() method
		return $task->execute($this->params);
	}
}