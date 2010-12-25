<?php

/**
 * Model for managing migrations
 **/
class Model_Minion_Migration extends Model
{
	/**
	 * Database connection to use
	 * @var Kohana_Database
	 */
	protected $_db = NULL;

	/**
	 * The table that's used to store the migrations
	 * @var string
	 */
	protected $_table = 'migrations';

	/**
	 * Constructs the model, taking a Database connection as the first and only 
	 * parameter
	 *
	 * @param Kohana_Database Database connection to use
	 */
	public function __construct(Kohana_Database $db)
	{
		$this->_db = $db;
	}

	/**
	 * Creates a new select query which includes all fields in the migrations 
	 * table plus a `id` field which is a combination of the timestamp and the 
	 * description
	 *
	 * @return Database_Query_Builder_Select
	 */
	protected function _select()
	{
		return DB::select('*', DB::expr('CONCAT(CAST(`timestamp` AS CHAR), "_", `description`) AS `id`'))->from($this->_table);
	}

	/**
	 * Selects all migrations from the migratinos table
	 *
	 * @return Kohana_Database_Result
	 */
	public function fetch_all()
	{
		return $this->_select()
			->execute($this->_db);
	}

	/**
	 * Fetches the latest version for all installed modules
	 *
	 * If a module does not have any applied migrations then no result will be 
	 * returned for it
	 *
	 * @return Kohana_Database_Result
	 */
	public function fetch_current_versions()
	{
		return $this->_select()
			->where('applied', '>', 0)
			->group_by('module')
			->execute();
	}

	/**
	 * Fetch a list of migrations that need to be applied in order to reach the 
	 * required version
	 *
	 * @param string Migration's Module
	 * @param string Target migration id
	 */
	public function fetch_required_migrations($modules = NULL, $target = NULL)
	{
		if( ! empty($modules) AND ! is_array($modules))
		{
			$modules = array($modules => $target);
		}

		// Get an array of the latest migrations, with the module name as the 
		// array key
		$migrations = $this->fetch_current_versions()->as_array('module');

		if(empty($modules))
		{
			$keys = array_keys($migrations);

			$modules = array_combine($keys, $keys);
		}

		$migrations_to_apply = array();

		// What follows is a bit of icky code, but there aren't many "nice" ways around it
		//
		// Basically we need to get a list of migrations that need to be performed, but 
		// the ordering of the migrations varies depending on whether we're wanting to 
		// migrate up or migrate down.  As such, we can't just apply a generic "order by x"
		// condition
		//
		// If you have a better way of doing this, please let me know :)

		foreach($modules as $module => $target)
		{
			$query = $this->_select()->or_where('module', '=', $module);

			// one of these conditions occurs if 
			// a) the user specified they want to bring this module up to date
			// or
			// b) if they just want to bring all modules up to date
			//
			// Basically this checks that the user hasn't explicitly specified a version
			// to migrate to
			if($target !== NULL AND $target !== $module)
			{
				list($timestamp, $description) = explode('_', $target, 2);

				$current_timestamp = isset($migrations[$module]) ? $migrations[$module]['timestamp'] : NULL;

				// If the current version is the requested version then nothing needs to be done
				if($current_timestamp === $timestamp)
				{
					continue;
				}

				$query->and_where('module', '=', $module);

				// If they haven't applied any migrations for this module
				// yet and are just wanting to apply all migrations (i.e. roll forward)
				if($current_timestamp === NULL)
				{
					$query
						->and_where('timestamp', '<=', $timestamp);
				}
				// If we need to move forward
				elseif($timestamp > $current_timestamp)
				{
					$query
						->and_where('timestamp', '<=', $timestamp)
						->and_where('applied',    '=',  0);
				}
				// If we want to roll back
				elseif($timestamp < $current_timestamp)
				{
					$query
						->and_where('timestamp',  '<', $current_timestamp)
						->and_where('timestamp', '>=', $timestamp)
						->and_where('applied',    '=', 1);
				}
				
				foreach($query->execute($this->_db) as $row)
				{
					$migrations_to_apply[$module][] = $row;
				}
			}

			unset($query);
		}

		return $migrations_to_apply;
	}
}
