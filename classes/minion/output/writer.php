<?php

/**
 * Base class for all output writers
 *
 * @author Matt Button <matthew@sigswitch.com>
 */
abstract class Minion_Output_Writer
{
	/**
	 * Accepts output from a task.
	 *
	 * Should also take an output type, $type which indicates what kind of 
	 * output is being passed on.  The value of $type will be one of the 
	 * constants defined in Minion_Output.
	 *
	 * @see Minion_Output
	 * @param string The output
	 * @param int    The output type
	 */
	abstract public function write($output, $type);
}
