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
	 * Syncs all available migration files with the database
	 *
	 * @chainable
	 * @return Minion_Migration_Manager Chainable instance
	 */
	public function sync()
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
