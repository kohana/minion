# Writing Tasks

Writing a minion tasks is very easy. 
Simply create a new class extending [Minion_Task] and call `Task_<Taskname>` in `classes/Task/` directory.

	class Task_Demo extends Minion_Task {

		/**
		 * @var array The list of options this task accepts and their default values.
		 */
		protected $_options = array('foo' = 'bar', 'bar' => NULL);

		/**
		 * This is a demo task.
		 * 
		 * @param array $params Contains parameters passed from CLI
		 * @return void
		 */
		protected function _execute(array $params)
		{
			if (empty($params['bar']))
			{
				$params['bar'] = Minion_CLI::read('Enter bar value');
			}
			$this->output->write('Bar: '.$params['bar']);
		}

	}

You'll notice a few things here:

 - You need a main [Minion_Task::_execute()] method. It should take one array parameter.
   - This parameter contains any command line options passed to the task.
   - For example, if you call the task above with 
   `./minion --task=demo --foo=foobar` then `$params` 
   will contain: `array('foo' => 'foobar', 'bar' => NULL)`
 - It needs to have a [Minion_Task::$_options] array. 
 This is a list of parameters you want to accept for this task. 
 Any parameters passed to the task not in this list will be rejected.

## Namespacing Tasks

You can 'namespace' tasks by placing them all in a subdirectory: `classes/Task/Database/Generate.php`. 
This task will be named `database:generate` and can be called with this task name.

# Parameter Validations

To add validations to your command line options, 
simply overload the [Minion_Task::build_validation()] method in your task:

	public function build_validation(Validation $validation)
	{
		return parent::build_validation($validation)
			->label('foo', 'Foo!')
			->rule('foo', 'not_empty') // Require this param
			->rule('bar', 'numeric');  // This param should be numeric
	}

These validations will run for every task call unless `--help` is passed to the task.

# Task Help

Tasks can have built-in help. Minion will read class docblocks that you specify:

	<?php defined('SYSPATH') OR die('No direct script access.');
	/**
	 * This is a demo task.
	 * 
	 * It can accept the following options:
	 *  - foo: this parameter does something. It is required.
	 *  - bar: this parameter does something else. It should be numeric.
	 * 
	 * @package   Kohana/Minion
	 * @category  Task
	 * @author    Kohana Team
	 * @copyright (c) 2009-2014 Kohana Team
	 * @license   http://kohanaframework.org/license
	 */
	class Task_Demo extends Minion_Task {

The `@` tags in the class comments will also be displayed in a human readable format. 
When writing your task comments, you should specify how to use it, and any parameters it accepts.

# Adding to index.php

Tasks are run by the CLI_Command class, which loads and runs them.


        // Bootstrap the application
        require APPPATH.'bootstrap'.EXT;

        // If PHP build's server API is CLI
        if (PHP_SAPI == 'cli')
        {
                /**
                 * Attempt to load and execute minion.
                 */
                class_exists('CLI_Command') OR die('Please enable the Minion module for CLI support.');
                set_exception_handler(array('Minion_Exception', 'handler'));

                CLI_Command::factory()->execute();
        }
        else
        // Request
        {

By default the the factory will use command line inputs, but you can override that by passing 
custom $params to the factory.

        $task = 'noop';
        $params = ['foo' => 'bar'];

        CLI_Command::factory($task)->execute($params);


This will instantiate a new Task with the given $task name, and will execute with $params.
When $params is provided, command line values are not loaded into the task.