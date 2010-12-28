# Minion

Minion is a module for the Kohana framework which allows you to run various tasks from the cli.

The main purpose of minion is to run database migrations, but it exposes a useful framework for creating
additional tasks.

The system is inspired by ruckusing, which was in turn inspired by rake.

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

## License

This is licensed under the [same license as kohana](http://kohanaframework.org/license)

## FAQ

### Can't I just create my own controllers instead of creating "tasks"

Yes, controllers offer just as much control as tasks, however there are a number of advantages to tasks:

* They can only be run via command line or through code (see note about http)
* All the groundwork for interacting with the user on the command line is already in place, you 
  just need to take advantage of it
* It provides a uniform way to access and perform tasks on the command line, rather than creating an elaborate
  collection of controllers while trying to restrict access to them.  If you create a module that requires command
  line interaction then you just ship a minion task with it and users will be able to start using it with minimal
  setup & configuration
