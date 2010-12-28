<?php

/**
 * The Migrate task compares the current version of the database with the target 
 * version and then executes the necessary commands to bring the database up to 
 * date
 *
 * Available config options are:
 *
 * db:migrate:version=version
 *
 *  The version to which the database should be migrated.  If this is NULL then 
 *  it will be updated to the latest available version
 *
 * db:migrate:locations=location[,location2[,location3...]]
 *
 *  A list of locations (under the migrations folder in the cascading 
 *  filesystem) that will be used to source migration files.  By default 
 *  migrations will be loaded from all available locations
 *
 * @author Matt Button <matthew@sigswitch.com>
 */
class Minion_Task_Db_Migrate extends Minion_Task
{
	/**
	 * Get a set of config options that migrations will accept
	 *
	 * @return array
	 */
	public function get_config_options()
	{
		return array(
			'version',
			'locations',
		);
	}

	/**
	 * Migrates the database to the version specified
	 *
	 * @param array Configuration to use
	 */
	public function execute(array $config)
	{
		$k_config = Kohana::config('minion/task/migrations');

		// Default is upgrade to latest
		$version = Arr::get($config, 'version', NULL);
		
		// Do fancy migration stuff here
	}
}
