<?php
/**
 * Minion task exception class.
 *
 * @package   Kohana/Minion
 * @category  Exception
 * @author    Kohana Team
 * @copyright (c) 2009-2014 Kohana Team
 * @license   http://kohanaframework.org/license
 */
abstract class Kohana_Minion_Task_Exception extends Minion_Exception {

	/**
	* Formating error message for display in CLI.
	*
	* @return string
	*/
	protected function _cli_format()
	{
		return I18n::translate('Task error').':'.PHP_EOL.$this->getMessage().PHP_EOL;
	}

}
