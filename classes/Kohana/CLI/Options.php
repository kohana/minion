<?php
/**
 * Minion task exception class.
 *
 * @package   Kohana/Minion
 * @category  Exception
 * @author    Kohana Team
 * @copyright (c) 2009-2014 Kohana Team
 * @license   http://kohanaframework.org/license
 */
class Kohana_CLI_Options extends Kohana_CLI_Input implements ArrayAccess {

	 /**
	 *
	 * @var string 
	 */
	protected $task = 'help';
	
	/**
	 *
	 * @var array 
	 */
	protected $params = [];

	/**
	 * 
	 */
	public function __construct()
	{
		$this->resolve($this->options());
	}
	
	/**
	 * 
	 * @param array $options
	 * @return \Kohana_Minion_Task_Options
	 */
	public function resolve(array $options = [])
	{
		if (isset($options['task']))
		{
			$this->task = $options['task'];
			unset($options['task']);
		}
		else if (isset($options[0]))
		{
			// The first positional argument (aka 0) may be the task name
			$this->task = $options[0];
			unset($options[0]);
		}
		
		$this->params = $options;
		
		return $this;
	}

	/**
	 * 
	 * @return string
	 */
	public function task()
	{
		return $this->task;
	}

	/**
	 * 
	 * @return array
	 */
	public function params()
	{
		return $this->params;
	}
	
	/**
	 * 
	 * @param type $param
	 * @return type
	 */
	public function get_param($param)
	{
		return $this->$param;
	}

	/**
	 * 
	 * @param type $name
	 * @return type
	 */
	public function __get($name)
	{
		return $this->params[$name];
	}
	
	/**
	 * 
	 * @param type $name
	 * @param type $value
	 */
	public function __set($name, $value)
	{
		$this->params[$name] = $value;
	}
	
	/**
	 * 
	 * @param  mixed $offset
	 * @return bool
	 */
	public function offsetExists($offset)
	{
		return array_key_exists($offset, $this->params);
	}

	/**
	 * 
	 * @param  mixed $offset
	 * @return mixed
	 */
	public function offsetGet($offset)
	{
		return $this->offsetExists($offset) ? $this->params[$offset] : NULL;
	}

	/**
	 * 
	 * @param mixed $offset
	 * @param mixed $value
	 */
	public function offsetSet($offset, $value)
	{
		$this->params[$offset] = $value;
	}

	/**
	 * 
	 * @param mixed $offset
	 */
	public function offsetUnset($offset)
	{
		unset($this->params[$offset]);
	}
}
