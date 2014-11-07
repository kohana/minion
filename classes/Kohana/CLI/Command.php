<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Command. Uses the [Options] class to determine what
 * [Task] to send the request to.
 *
 * @package    Kohana
 * @category   Base
 * @author     Kohana Team
 * @copyright  (c) 2008-2012 Kohana Team
 * @license    http://kohanaframework.org/license
 */
class Kohana_CLI_Command
{
	/**
	 * Unix command exit status
	 * @link http://php.net/manual/en/function.exit.php
	 */
	const SUCCESS = 0;
	const FAIL = 1;
	
	/**
	 *
	 * @var CLI_Command_Client 
	 */
	protected $_client;
	
	/**
	 *
	 * @var string
	 */
	protected $_task;

	/**
	 * 
	 * @param string $task
	 * @param array  $params
	 * @return \CLI_Command
	 */
	public static function factory($task = TRUE, $params = [])
	{
		$options = CLI::factory('Options');
		
		if ($task===TRUE)
		{
			$task = $options->task();
		}
		
		$params = count($params) ? $params : $options->params();
		
		$command = new CLI_Command($task, $params);
		
		return $command;
	}
	
	/**
	 * 
	 * @param string $task
	 * @param array  $params
	 */
	public function __construct($task, $params)
	{
		$this->_task = $task;
		$this->_client = new CLI_Command_Client($params);
		
	}
	
	/**
	 * Gets the resolved Task name
	 * 
	 * @return string
	 */
	public function task()
	{
		return $this->_task;
	}
	
	/**
	 * 
	 */
	public function execute()
	{
		$status = $this->_client->execute($this);
		
		exit($status);
	}
}
