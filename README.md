# Minion

| ver   | Stable                                                                                                                           | Develop                                                                                                                            |
|-------|----------------------------------------------------------------------------------------------------------------------------------|------------------------------------------------------------------------------------------------------------------------------------|
| 3.3.x | [![Build Status - 3.3/master](https://travis-ci.org/kohana/minion.svg?branch=3.3%2Fmaster)](https://travis-ci.org/kohana/minion) | [![Build Status - 3.3/develop](https://travis-ci.org/kohana/minion.svg?branch=3.3%2Fdevelop)](https://travis-ci.org/kohana/minion) |
| 3.4.x | [![Build Status - 3.4/master](https://travis-ci.org/kohana/minion.svg?branch=3.4%2Fmaster)](https://travis-ci.org/kohana/minion) | [![Build Status - 3.4/develop](https://travis-ci.org/kohana/minion.svg?branch=3.4%2Fdevelop)](https://travis-ci.org/kohana/minion) |

Minion is a [Kohana](http://github.com/kohana) module for running tasks via the CLI.

The system is inspired by ruckusing, which had a nice system for defining tasks 
but lacked the desired flexibility for kohana integration.

## Getting Started

First off, download and enable the module in your bootstrap

Then copy the bash script `minion` alongside your index.php (most likely the webroot).
If you'd rather the executable be in a different location to `DOCROOT/index.php` 
then simply modify the bash script to point to index.php.

You can then run minion like so:

	./minion --task=<name>

To view a list of minion tasks, run minion without any parameters, or with the `--help` option:

	./minion
	./minion --help

To view help for a specific minion task run:

	./minion --task=<name> --help

For security reasons Minion will only run from the CLI. 
Attempting to access it over http will cause a `Kohana_Exception` to be thrown.

If you're unable to use the binary file for whatever reason then simply 
replace `./minion --task=<name>` in the above examples with:

	php index.php --task=<name> --uri=minion --text="text string"

## Writing your own tasks

All minion tasks must be located in `classes/Task/`. They can be in any module, 
thus allowing you to ship custom minion tasks with your own module or product.

Each task must extend the abstract class `Minion_Task` and implement `Minion_Task::_execute()`.

See `Minion_Task` for more details.

## Documentation

Code should be commented well enough not to need documentation, and minion can extract a class doccomment 
to use as documentation on the CLI.

## Testing

This module is unittested using the [unittest module](http://github.com/kohana/unittest).
You can use the `minion` group to only run minion tests.

i.e.

	phpunit --group minion

Feel free to contribute tests(!), they can be found in the `tests/minion` directory.

## License

This is licensed under the [same license as Kohana](http://kohanaframework.org/license).
