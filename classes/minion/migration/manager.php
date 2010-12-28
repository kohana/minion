<?php

/**
 * The migration manager is responsible for locating migration files, syncing 
 * them with the migrations table in the database and selecting any migrations 
 * that need to be executed in order to reach a target version
 *
 * @author Matt Button <matthew@sigswitch.com>
 **/
class Minion_Migration_Manager {

	/**
	 * The database connection that sould be used
	 * @var Kohana_Database
	 */
	protected $_db;

	/**
	 * Constructs the object, allows injection of a Database connection
	 *
	 * @param Kohana_Database The database connection that the manager should use
	 */
	public function __construct(Kohana_Database $db)
	{
		$this->_db = $db;
	}

	/**
	 * Run migrations in the specified locations so as to reach specified targets
	 *
	 * There are three methods for specifying target versions:
	 *
	 * 1. Pass them in with the array of locations, i.e.
	 *
	 *     array(
	 *       location => target_version
	 *     )
	 *
	 * 2. Pass them in separately, with param1 containing an array of 
	 * locations like:
	 *
	 *     array(
	 *       location,
	 *       location2,
	 *     )
	 *
	 * And param2 containing an array structured in the same way as in #1
	 *
	 * 3. Perform a mix of the above two methods
	 *
	 * It may seem odd to use two arrays to specify locations and versions, but 
	 * it's this way to allow users to upgrade / downgrade all locations while 
	 * migrating a specific location to a specific version
	 *
	 * If no locations are specified then migrations from all locations will be 
	 * run and be brought up to the latest available version
	 *
	 * @param  array   Set of locations to update, empty array means all
	 * @param  array   Versions for specified locations
	 * @param  boolean The default direction (up/down) for migrations without a specific version
	 * @return boolean Whether
	 */
	public function run_migration(array $locations = array(), $versions = array(), $default_direction = TRUE)
	{
		
	}

	/**
	 * Syncs all available migration files with the database
	 *
	 * @chainable
	 * @return Minion_Migration_Manager Chainable instance
	 */
	public function sync_migration_files()
	{
		$model = new Model_Minion_Migration($this->_db);

		$installed = $model->fetch_all();

		$available = $this->scan_for_migrations();
	}

	/**
	 * Scans all migration directories for available migration files
	 *
	 * Returns an array of 
	 *
	 *   migration_id => array(
	 *   	'file'   => migration_file, 
	 *   	'location' => migration_location
	 *   );
	 *
	 * @param return array
	 */
	public function scan_for_migrations()
	{
		$files = Kohana::list_files('migrations');

		return Minion_Migration_Util::parse_migrations_from_files($files);
	}
}
