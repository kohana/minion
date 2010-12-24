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
	 */
	abstract public function up();

	/**
	 * Runs any SQL queries necessary to bring the database schema down a version
	 *
	 */
	abstract public function down();
}
