<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Displays the current status of migrations in all locations
 *
 * This task takes no config options
 *
 * @author Matt Button <matthew@sigswitch.com>
 */
class Kohana_Minion_Task_Db_Status extends Kohana_Minion_Task
{
	/**
	 * Execute the task
	 *
	 * @param array Config for the task
	 */
	public function execute(array $config)
	{
		$db = Database::instance();
		$model = new Model_Minion_Migration($db);

		$view = new View('minion/task/db/status');

		$view->locations = $model->get_location_statuses();

		echo $view;
	}

}
