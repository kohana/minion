<?php defined('SYSPATH') or die('No direct script access.');
/**
 * This is a demo task.
 * 
 * It can accept the following options:
 *  - foo: this parameter does something. It is required.
 *  - bar: this parameter does something else. It should be numeric.
 *
 * @package    Kohana
 * @category   Helpers
 * @author     Kohana Team
 * @copyright  (c) 2009-2011 Kohana Team
 * @license    http://kohanaframework.org/license
 */
class Minion_Task_Demo extends Minion_Task
{
	protected $_defaults = array(
		'foo' => 'bar',
		'bar' => NULL,
	);

	/**
	 * 
	 *
	 * @return 
	 */
	protected function _execute(array $params)
	{
		// var_dump(valid::email('mcnama1_patr@bentley.edu'));
		var_dump($params);
		echo 'foobar';
	}

	public function build_validation(Validation $validation)
	{
		return parent::build_validation($validation)
			->rule('foo', 'not_empty') // Require this param
			->rule('bar', 'numeric');
	}
}