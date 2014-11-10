<?php

return [
	'task' => function($name)
	{
		return Minion_Task::factory($name);
	},
	'view' => function($file=NULL, $data=NULL)
	{
		return View::factory($file, $data);
	},
	'validation' => function($array = [])
	{
		return Validation::factory($array);
	}
];