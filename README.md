# Minion

*NOTE: Minion is currently in a beta state, please report any issues in the issue tracker*

Minion is a module for running database migrations located in the Kohana cascading filesystem, but it also provides a useful framework for creating cli based tasks.

The original "need" behind Minion was the lack of a good db migrations system, capable of selecting migrations
from multiple locations (i.e. different modules).

The system is inspired by ruckusing, which had a nice system for defining tasks but lacked the desired flexibility.

**Please don't PM me with support / feature requests, instead create an issue in [the tracker](https://github.com/BRMatt/kohana-minion/issues).**

## Requirements

* [kohana-database](https://github.com/kohana/database) is required by the migration tasks

## Compatibility

Minion should be compatible with both Kohana 3.0.x and 3.1.x

## Getting Started

First off, download and enable the module in your bootstrap

Then copy the bash script `minion` alongside your index.php (most likely the webroot).
If you'd rather the executable be in a different location to index.php then simply modify the bash script to point to index.php.

You can then run minion like so:

	./minion {task}

To view a list of minion tasks, run minion without any parameters, or with the `--help` option

	./minion
	./minion --help

To view help for a specific minion task run

	./minion {task} --help

For security reasons Minion will only run from the cli.  Attempting to access it over http will cause
a `Kohana_Exception` to be thrown.

If you're unable to use the binary file for whatever reason then simply replace `./minion {task}` in the above 
examples with 

	php index.php --uri=minion --task={task}

## Writing your own tasks

All minion tasks must be located in `classes/minion/task/`.  They can be in any module, thus allowing you to 
ship custom minion tasks with your own module / product.

Each task must extend the abstract class `Minion_Task` and implement `Minion_Task::execute()`.

See `Minion_Task` for more details.

## Documentation

Code should be commented well enough not to need documentation, and minion can extract a class' doccomment to use
as documentation on the cli.

## Testing

This module is unittested using the [unittest module](http://github.com/kohana/unittest).
You can use the `minion` group to only run minion tests.

i.e.

	phpunit --group minion

Feel free to contribute tests(!), they can be found in the `tests/minion` directory. :)

## License

This is licensed under the [same license as Kohana](http://kohanaframework.org/license).

This project is not endorsed by the Kohana Framework project.

## FAQ

### Can't I just create my own controllers instead of creating "tasks"

Yes, controllers offer just as much control as tasks, however there are a number of advantages to tasks:

* They can only be run via command line or through code (see note about http)
* All the groundwork for interacting with the user on the command line is already in place, you 
  just need to take advantage of it
* It provides a uniform way to access and perform tasks on the command line, rather than creating an elaborate
  collection of controllers while trying to restrict access to them.  If you create a module that requires command
  line interaction then you just ship a minion task with it and users will be able to start using it with minimal
  setup &amp; configuration

### Eeew why aren't you using ORM xyz?

In order to prevent conflicts across installations Minion aims to have as few dependencies as possible.

### This is awesome! How can I contribute?

Thanks for wanting to help out, just fork, commit, push, and send a pull request :)

### UR DOIN IT WRONG

Sorry you feel that way, it'd be useful if you could create an issue outlining what you think should be changed.