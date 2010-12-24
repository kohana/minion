<?php


Route::set('minion', 'minion(/<action>)(/<task>)', array('action' => 'help'))
	->defaults(array(
		'controller' => 'minion',
		'action'     => 'execute',
	));
