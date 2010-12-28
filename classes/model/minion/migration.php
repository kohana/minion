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
	 * Fetches the latest version for all installed locations
	 *
	 * If a location does not have any applied migrations then no result will be 
	 * returned for it
	 *
	 * @return Kohana_Database_Result
	 */
	public function fetch_current_versions()
	{
		return $this->_select()
			->where('applied', '>', 0)
			->group_by('location')
			->execute();
	}

	/**
	 * Fetch a list of migrations that need to be applied in order to reach the 
	 * required version
	 *
	 * @param string Migration's location
	 * @param string Target migration id
	 */
	public function fetch_required_migrations($locations = NULL, $target = TRUE)
	{
		if( ! empty($locations) AND ! is_array($locations))
		{
			$locations = array($locations => $target);
		}

		// Get an array of the latest migrations, with the location name as the 
		// array key
		$migrations = $this->fetch_current_versions()->as_array('location');

		if(empty($locations))
		{
			$keys = array_keys($migrations);

			$locations = array_combine($keys, $keys);
		}

		$migrations_to_apply = array();

		// What follows is a bit of icky code, but there aren't many "nice" ways around it
		//
		// Basically we need to get a list of migrations that need to be performed, but 
		// the ordering of the migrations varies depending on whether we're wanting to 
		// migrate up or migrate down.  As such, we can't just apply a generic "order by x"
		// condition, we have to run an individual query for each location
		//
		// Again, icky, but this appears to be the only "sane" way of doing it with multiple
		// locations
		//
		// If you have a better way of doing this, please let me know :)

		foreach($locations as $location => $target)
		{
			// By default all migrations go "up"
			$migrations_to_apply[$location]['direction']  = 1;
			$migrations_to_apply[$location]['migrations'] = array();
			
			$query = $this->_select()->where('location', '=', $location);

			// one of these conditions occurs if 
			// a) the user specified they want to bring this location up to date
			// or
			// b) if they just want to bring all locations up to date
			//
			// Basically this checks that the user hasn't explicitly specified a version
			// to migrate to
			if(is_bool($target) OR $target === $location)
			{
				// We're "undoing" all applied migrations, i.e. rolling back
				if($target === FALSE)
				{
					$migrations_to_apply[$location]['direction'] = -1;
					
					$query
						->where('applied', '=', 1)
						->order_by('timestamp', 'DESC');
				}
				// We're rolling forward
				else
				{
					$query
						->where('applied', '=', 0)
						->order_by('timestamp', 'ASC');
				}
			}
			// Else if the user explicitly specified a target version of some kind
			else
			{
				list($timestamp, $description) = explode('_', $target, 2);

				$current_timestamp = isset($migrations[$location]) ? $migrations[$location]['timestamp'] : NULL;

				// If the current version is the requested version then nothing needs to be done
				if($current_timestamp === $timestamp)
				{
					continue;
				}

				$query->where('location', '=', $location);

				// If they haven't applied any migrations for this location
				// yet and are justwhere wanting to apply all migrations (i.e. roll forward)
				if($current_timestamp === NULL)
				{
					$query
						->and_where('timestamp', '<=', $timestamp)
						->order_by('timestamp', 'ASC');
				}
				// If we need to move forward
				elseif($timestamp > $current_timestamp)
				{
					$query
						->and_where('timestamp',  '<=', $timestamp)
						->and_where('applied',    '=',  0)
						->order_by('timestamp', 'ASC');
				}
				// If we want to roll back
				elseif($timestamp < $current_timestamp)
				{
					$query
						->and_where('timestamp',  '<', $current_timestamp)
						->and_where('timestamp',  '>', $timestamp)
						->and_where('applied',    '=', 1)
						->order_by('timestamp', 'DESC');

					$migrations_to_apply[$location]['direction'] = -1;
				}
			}

			foreach($query->execute($this->_db) as $row)
			{
				$migrations_to_apply[$location]['migrations'][] = $row;
			}

			unset($query);
		}

		return $migrations_to_apply;
	}
}
