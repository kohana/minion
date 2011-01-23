<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Minion exception, thrown during a migration error
 */
class Kohana_Minion_Migration_Exception extends Kohana_Exception
{
	protected $_migration = array();

	/**
	 * Constructor
	 *
	 */
	public function __construct($message, array $migration, array $variables = array(), $code = 0)
	{
		$variables[':migration-id'] = $migration['id'];
		$variables[':migration-location'] = $migration['location'];

		$this->_migration = $migration;

		parent::__construct($message, $variables, $code);
	}

	/**
	 * Get the migration that caused this exception to be thrown
	 *
	 * @return array
	 */
	public function get_migration()
	{
		return $this->_migration;
	}

}
