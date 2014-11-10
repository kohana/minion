<?php

Trait Builders {
	
	/**
	 *
	 * @var array 
	 */
	protected $builders = [];

	/**
	 * 
	 * @param string $name
	 * @return Closure
	 */
	public function get_builder($name)
	{
		return $this->builders[$name];
	}
	
	
	/**
	 * 
	 * @param string  $name
	 * @param Closure $builder
	 * @return mixed
	 */
	public function set_builder($name, Closure $builder)
	{
		$this->builders[$name] = $builder;
		return $this;
	}


	/**
	 * 
	 * @param string $name
	 * @param array  $params
	 * @return mixed
	 */
	protected function call_builder($name, $params = [])
	{
		return call_user_func_array($this->builders[$name], $params);
	}
	
}
