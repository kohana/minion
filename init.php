<?php defined('SYSPATH') or die('No direct script access.');

Route::set('minion', 'minion(/<action>)(/<task>)', array('action' => 'help'))
	->defaults(array(
		'controller' => 'minion',
		'action'     => 'execute',
	));
