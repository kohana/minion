# Minion

Minion is a module for the Kohana framework which allows you to run various tasks from the cli.

## Getting Started

First off, download and enable the module in your bootstrap

Then you can run minion like so:

	php index.php --uri=minion/{task}

To view a list of minion tasks, run 

	php index.php --uri=minion/help

To view help for a specific minion task run

	php index.php --uri=minion/help/{task}

For security reasons Minion will only run from the cli.  Attempting to access it over http will cause
a `Request_Exception` to be thrown.

## Writing your own tasks

All minion tasks must be located in `classes/minion/task/`.  They can be in any module, thus allowing you to 
ship custom minion tasks with your own module / product.

Each task must extend the abstract class `Minion_Task` and implement `Minion_Task::get_config_options()` and `Minion_Task::execute()`.
See `Minion_Task` for more details.

## Documentation

Code should be commented well enough not to need documentation, and minion can extract a class' doccomment to use
as documentation on the cli.

## Testing

This module is unittested using the [unittest module](http://github.com/kohana/unittest).  You can use the `minion` group to only run
minion tests.

i.e.

	phpunit --group minion
