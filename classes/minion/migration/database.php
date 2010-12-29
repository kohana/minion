<?php

/**
 * A faux database connection for doing dry run migrations
 */
class Minion_Migration_Database extends Database_MySQL {

	/**
	 * Creates a disposable instance of the faux connection
	 * 
	 * @param array Config for the underlying DB connection
	 * @return Minion_Migration_Database
	 */
	public static function instance(array $config)
	{
		return new Minion_Migration_Database('_minion_faux', $config);
	}

	/**
	 * The query stack used to store queries
	 * @var array
	 */
	protected $_queries = array();

	/**
	 * Gets the stack of queries that have been executed
	 * @return array
	 */
	public function get_query_stack()
	{
		return $this->_queries;
	}

	/**
	 * Resets the query stack to an empty state
	 * @return Minion_Migration_Database $this
	 */
	public function reset_query_stack()
	{
		$this->_queries = array();

		return $this;
	}

	/**
	 * Appears to allow calling script to execute an SQL query, but merely logs 
	 * it and returns NULL
	 *
	 * @return NULL
	 */
	public function query($type, $sql, $as_object = FALSE, array $params = NULL)
	{
		$this->_queries[] = $sql;

		return NULL;
	}
}
