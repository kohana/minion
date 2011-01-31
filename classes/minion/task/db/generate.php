<?php defined('SYSPATH') or die('No direct script access.');

/**
 * The generate task provides an easy way to create migration files
 *
 * Available config options are:
 *
 * --location=path/to/migration/location
 *  
 *  This is a required config option, use it specify in which location the 
 *  migration should be stored.  Due to the nature of the cascading filesystem 
 *  minion doesn't automatically know where a migration is stored so make sure 
 *  you pass in the full path to your migrations folder, e.g.
 *
 *  # The location of the migrations folder is modules/myapp/migrations/myapp/
 *  --location=modules/myapp/migrations/myapp/
 *
 *  On nix based systems you should be able to tab complete the path
 *
 * --description="Description of migration here"
 *
 *  This is an arbitrary description of the migration, used to build the 
 *  filename.  It is required but can be changed manually later on without 
 *  affecting the integrity of the migration.
 *
 *  The description will be 
 *
 * @author Matt Button <matthew@sigswitch.com>
 */
class Minion_Task_Db_Generate extends Minion_Task
{
	/**
	 * A set of config options that this task accepts
	 * @var array
	 */
	protected $_config = array(
		'location',
		'description'
	);

	/**
	 * Execute the task
	 *
	 * @param array Configuration
	 */
	public function execute(array $config)
	{
		if (empty($config['location']) OR empty($config['description']))
		{
			return 'Please provide --location and --description'.PHP_EOL.
			       'See help for more info'.PHP_EOL;
		}

		$location    = rtrim(realpath($config['location']), '/').'/';
		$description = $config['description'];

		// {year}{month}{day}{hour}{minute}{second}
		$time  = date('YmdHis');
		$class = $this->_generate_classname($location, $time);
		$file  = $this->_generate_filename($location, $time, $description);


		$data = Kohana::FILE_SECURITY.View::factory('minion/task/db/generate/template')
			->set('class', $class)
			->set('description', $description)
			->render();

		file_put_contents($file, $data);

		return 'Migration generated in '.$file.PHP_EOL;
	}

	/**
	 * Generate a class name from the location
	 *
	 * @param  string location
	 * @param  string Timestamp
	 * @return string Class name
	 */
	protected function _generate_classname($location, $time)
	{
		// Chop up everything up until the relative path
		$location = substr($location, strrpos($location, 'migrations/') + 11);

		$class = ucwords(str_replace('/', ' ', $location));

		// If location is empty then we want to avoid double underscore in the 
		// class name
		if ( ! empty($class))
		{
			$class .= '_';
		}

		$class .= $time;

		return 'Migration_'.preg_replace('~[^a-zA-Z0-9]+~', '_', $class);
	}

	/**
	 * Generates a filename from the location, time and description
	 *
	 * @param  string Location to store migration
	 * @param  string Timestamp
	 * @param  string Description
	 * @return string Filename
	 */
	public function _generate_filename($location, $time, $description)
	{
		$description = substr(strtolower($description), 0, 100);

		return $location.$time.'_'.preg_replace('~[^a-z]+~', '-', $description).EXT;
	}

}
