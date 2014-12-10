<?php

interface Kohana_Task {

	/**
	 * Unix command exit status
	 *
	 * @link http://php.net/manual/en/function.exit.php
	 */
	const SUCCESS = 0;

	const FAIL = 1;

	/**
	 *
	 * @param array $params
	 */
	function execute(array $params);
}
