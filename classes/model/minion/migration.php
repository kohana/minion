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
		return DB::select('*', DB::expr('CONCAT(`location`, ":", CAST(`timestamp` AS CHAR)) AS `id`'))->from($this->_table);
	}

	/**
	 * Inserts a migration into the database
	 *
	 * @param array Migration data
	 * @return Model_Minion_Migration $this
	 */
	public function add_migration(array $migration)
	{
		DB::insert($this->_table, array('timestamp', 'location', 'description'))
			->values(array($migration['timestamp'], $migration['location'], $migration['description']))
			->execute($this->_db);

		return $this;
	}

	/**
	 * Deletes a migration from the database
	 *
	 * @param string|array Migration id / info
	 * @return Model_Minion_Migration $this
	 */
	public function delete_migration($migration)
	{
		if(is_array($migration))
		{
			$timestamp = $migration['timestamp'];
			$location  = $migration['location'];
		}
		else
		{
			list($timestamp, $location) = explode(':', $migration);
		}

		DB::delete($this->_table)
			->where('timestamp', '=', $timestamp)
			->where('location',  '=', $location)
			->execute($this->_db);

		return $this;
	}

	/**
	 * Update an existing migration record to reflect a new one
	 *
	 * @param array The current migration
	 * @param array The new migration
	 * @return Model_Minion_Migration $this
	 */
	public function update_migration(array $current, array $new)
	{
		$set = array();
		
		foreach($new as $key => $value)
		{
			if($key !== 'id' AND $current[$key] !== $value)
			{
				$set[$key] = $value;
			}
		}

		if(count($set))
		{
			DB::update($this->_table)
				->set($set)
				->where('timestamp', '=', $current['timestamp'])
				->where('location', '=', $current['location'])
				->execute($this->_db);
		}

		return $this;
	}

	/**
	 * Change the applied status for a migration
	 *
	 * @param  array Migration information
	 * @param  bool  Whether this migration has been applied or unapplied
	 * @return Model_Minion_Migration
	 */
	public function mark_migration(array $migration, $applied)
	{
		DB::update($this->_table)
			->set(array('applied' => (int) $applied))
			->where('timestamp', '=', $migration['timestamp'])
			->where('location',  '=', $migration['location'])
			->execute($this->_db);

		return $this;
	}

	/**
	 * Selects all migrations from the migratinos table
	 *
	 * @return Kohana_Database_Result
	 */
	public function fetch_all($key = NULL, $value = NULL)
	{
		return $this->_select()
			->execute($this->_db)
			->as_array($key, $value);
	}

	/**
	 * Fetches the latest version for all installed locations
	 *
	 * If a location does not have any applied migrations then no result will be 
	 * returned for it
	 *
	 * @return Kohana_Database_Result
	 */
	public function fetch_current_versions($key = 'location', $value = NULL)
	{
		// Little hack needed to do an order by before a group by
		return DB::select()
			->from(array(
				$this->_select()
				->where('applied', '>', 0)
				->order_by('timestamp', 'DESC'),
				'temp_table'
			))
			->group_by('location')
			->execute($this->_db)
			->as_array($key, $value);
	}

	/**
	 * Fetch a list of migrations that need to be applied in order to reach the 
	 * required version
	 *
	 * @param string  Migration's location
	 * @param string  Target migration id
	 * @param boolean Default direction of versionless migrations 
	 */
	public function fetch_required_migrations($locations = NULL, $target_version = TRUE, $default_direction = TRUE)
	{
		if( ! empty($locations) AND ! is_array($locations))
		{
			$locations = array(
				$locations => is_array($target_version) 
								? $default_direction 
								: $target_version
			);
		}

		// Get an array of the latest migrations, with the location name as the 
		// array key
		$migrations = $this->fetch_current_versions('location');

		// The user wants to run all available migrations
		if(empty($locations))
		{
			if(count($migrations))
			{
				$keys = array_keys($migrations);

				$locations = array_combine($keys, $keys);
			}
			else
			{
				$locations = $this->fetch_all('location', 'location');
			}
		}
		// If the calling script has been lazy and given us a numerically 
		// indexed array of locations then we need to convert it to a mirrored 
		// array
		//
		// We will decide the target version for these within the loop below
		elseif( ! Arr::is_assoc($locations))
		{
			foreach($locations as $_pos => $location)
			{
				unset($locations[$_pos]);

				$locations[$location] = $location;
			}
		}

		// Merge locations with specified target versions 
		if( ! empty($target_version) AND is_array($target_version))
		{
			$locations = $target_version + $locations;
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
			$migrations_to_apply[$location]['direction']  = TRUE;
			$migrations_to_apply[$location]['migrations'] = array();
			
			$query = $this->_select()->where('location', '=', $location);

			// If this migration was auto-selected from the db then use the 
			// default migration direction
			if($target === $location)
			{
				$target = is_bool($target_version) 
					? $target_version 
					: (bool) $default_direction;
			}

			// If the user is rolling this location to either extreme up or 
			// extreme down
			if(is_bool($target))
			{
				// We're "undoing" all applied migrations, i.e. rolling back
				if($target === FALSE)
				{
					$migrations_to_apply[$location]['direction'] = FALSE;
					
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
				$timestamp         = $target;
				$current_timestamp = isset($migrations[$location]) 
					? $migrations[$location]['timestamp'] 
					: NULL;

				// If the current version is the requested version then nothing 
				// needs to be done
				if($current_timestamp === $timestamp)
				{
					continue;
				}

				// If they haven't applied any migrations for this location
				// yet and are justwhere wanting to apply all migrations 
				// (i.e. roll forward)
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

					$migrations_to_apply[$location]['direction'] = FALSE;
				}
			}

			$migrations_to_apply[$location]['migrations'] = $query->execute($this->_db)->as_array();

			unset($query);
		}

		return $migrations_to_apply;
	}
}
