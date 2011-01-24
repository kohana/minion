<?php

/**
 * Purges the application file cache
 *
 * @author Matt Button <matthew@sigswitch.com>
 **/
class Minion_Task_Cache_Purge extends Minion_Task
{

	/**
	 * Gets a set of config options this minion task accepts
	 *
	 * @return array
	 */
	public function get_config_options()
	{
		return array();
	}

	/**
	 * Clears the cache
	 */
	public function execute(array $config)
	{
	
	}
}
