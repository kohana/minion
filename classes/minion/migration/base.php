<?php

/**
 * The base migration class, must be extended by all migration files
 *
 * Each migration file must implement an up() and a down() which are used to 
 * apply / remove this migration from the schema respectively
 *
 * @author Matt Button <matthew@sigswitch.com>
 */
abstract class Minion_Migration_Base {

	/**
	 * Runs any SQL queries necessary to bring the database up a migration version
	 *
	 * @param Kohana_Database The database connection to perform actions on
	 */
	abstract public function up(Kohana_Database $db);

	/**
	 * Runs any SQL queries necessary to bring the database schema down a version
	 *
	 * @param Kohana_Database The database connection to perform actions on
	 */
	abstract public function down(Kohana_Database $db);
}
