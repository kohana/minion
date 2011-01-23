<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Create test_minion_migrations table
 */
class Migration_Minion_20110110093709 extends Kohana_Minion_Migration_Base {

	/**
	 * Run queries needed to apply this migration
	 *
	 * @param Kohana_Database Database connection
	 */
	public function up(Kohana_Database $db)
	{
		$db->query(NULL, 'CREATE TABLE `test_minion_migrations` (
  `timestamp` varchar(14) NOT NULL,
  `description` varchar(100) NOT NULL,
  `location` varchar(100) NOT NULL,
  `applied` tinyint(1) DEFAULT \'0\',
  PRIMARY KEY (`timestamp`,`location`),
  UNIQUE KEY `MIGRATION_ID` (`timestamp`,`description`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;');
	}

	/**
	 * Run queries needed to remove this migration
	 *
	 * @param Kohana_Database Database connection
	 */
	public function down(Kohana_Database $db)
	{
		$db->query(NULL, 'DROP TABLE `test_minion_migrations`');
	}
}
