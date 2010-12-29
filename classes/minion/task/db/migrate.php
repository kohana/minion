<?php

/**
 * The Migrate task compares the current version of the database with the target 
 * version and then executes the necessary commands to bring the database up to 
 * date
 *
 * Available config options are:
 *
 * --environment=environment
 *
 *  Specify the "environment" that you're currently in.  You can change the 
 *  environment => db config group mapping in the minion/migration config file.
 *
 * --versions=[location:]version
 *
 *  Used to specify the version to migrate the database to.  The location prefix 
 *  is used to specify the target version of an individual location. Version
 *  specifies the target version, which can be either:
 *
 *     * A migration version (migrates up/down to that version)
 *     * TRUE (runs all migrations to get to the latest version)
 *     * FALSE (undoes all appled migrations)
 *
 *  An example of a migration version is 20101229015800
 *
 *  If you specify TRUE / FALSE without a location then the default migration 
 *  direction for locations without a specified version will be up / down respectively.
 *
 *  If you're only specifying a migration version then you *must* specify a location
 *
 * --locations=location[,location2[,location3...]]
 *
 *  A list of locations (under the migrations folder in the cascading 
 *  filesystem) that will be used to source migration files.  By default 
 *  migrations will be loaded from all available locations
 *
 * --dry-run
 *
 *  No value taken, if this is specified then instead of executing the SQL it 
 *  will be printed to the console
 *
 * @author Matt Button <matthew@sigswitch.com>
 */
class Minion_Task_Db_Migrate extends Minion_Task
{

	/*
	 * Th' default direction for migrations, TRUE = up, FALSE = down
	 * @var boolean
	 */
	protected $_default_direction = TRUE;

	/**
	 * A set of config options that this task accepts
	 * @var array
	 */
	protected $_config = array(
		'environment',
		'versions',
		'locations',
		'dry-run'
	);

	/**
	 * Migrates the database to the version specified
	 *
	 * @param array Configuration to use
	 */
	public function execute(array $config)
	{
		$k_config = Kohana::config('minion/migration');

		// Grab user input, using sensible defaults
		$environment         = Arr::get($config, 'environment', 'development');
		$specified_locations = Arr::get($config, 'locations',   NULL);
		$versions            = Arr::get($config, 'versions',    NULL);
		$dry_run             = isset($config['dry-run']);

		$targets   = $this->_parse_target_versions($versions);
		$locations = $this->_parse_locations($specified_locations);


		$db = Database::instance($k_config['db_connections'][$environment]);

		if($dry_run)
		{
			$manager = new Minion_Migration_Manager(
				Minion_Migration_Database::instance($k_config['db_connections'][$environment]),
				new Model_Minion_Migration($db)
			);
		}
		else
		{
			$manager = new Minion_Migration_Manager($db);
		}

		$results = $manager
			// Sync the available migrations with those in the db
			->sync_migration_files()
			// Run migrations for specified locations & versions, and if it's 
			// a dry run don't log results to DB
			->run_migration($locations, $targets, $this->_default_direction, ! $dry_run);
	}

	/**
	 * Parses a comma delimted set of locations and returns an array of them
	 *
	 * @param  string Comma delimited string of locations
	 * @return array  Locations
	 */
	protected function _parse_locations($location)
	{
		if(is_array($location))
			return $location;

		$locations = array();
		$location  = trim($location, ',');

		if( ! empty($location))
		{
			foreach($location as $a_location)
			{
				$locations[] = trim($a_location, '/');
			}
		}

		return $locations;
	}

	/**
	 * Parses a set of target versions from user input
	 *
	 * Valid input formats for targets are:
	 *
	 *    TRUE
	 *
	 *    FALSE
	 *
	 *    {location}:(TRUE|FALSE|{migration_id})
	 *
	 * @param  string Target version(s) specified by user
	 * @return array  Versions
	 */
	protected function _parse_target_versions($versions)
	{
		if(empty($versions))
			return array();

		$targets = array();

		if( ! is_array($versions))
		{
			$versions = explode(',', trim($versions));
		}

		foreach($versions as $version)
		{
			$target = $this->_parse_version($version);

			if(is_array($target))
			{
				list($location, $version) = $target;

				$targets[$location] = $version;
			}
			else
			{
				$this->_default_direction = $target;
			}
		}

		return $targets;
	}

	/*
	 * Helper function for parsing target versions in user input
	 *
	 * @param  string         Input migration target
	 * @return boolean|string The parsed target
	 */
	protected function _parse_version($version)
	{
		if(is_bool($version))
			return $version;

		if($version === 'TRUE' OR $version == FALSE)
			return $version === 'TRUE';

		if(strpos(':', $version) !== FALSE)
			return explode(':', $version);

		throw new Kohana_Exception('Invalid target version :version', array(':version' => $version));
	}
}
