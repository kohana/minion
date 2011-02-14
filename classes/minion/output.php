<?php

/**
 * Class for handling output from Minion Tasks
 *
 * The concept is similar to the Kohana Logging system in that you have multiple
 * writers that can accept output and display it in their different ways
 * depending on the type of output received from the task (e.g. ERROR, WARNING
 * etc.).
 *
 * However, unlike the logs, output is not associated with the time it was
 * generated.
 *
 * @author Matt Button <matthew@sigswitch.com>
 */
class Minion_Output
{
	// Output levels
	const ERROR   = 1;
	const WARNING = 2;
	const INFO    = 4;
	const SUCCESS = 8;

	/**
	 * A singleton instance of the output class.
	 * Public for testing purposes
	 * @var Minion_Output
	 */
	public static $_instance;

	/**
	 * Get the singleton instance of Minion_Output
	 *
	 * @return Minion_Output
	 */
	public static function instance()
	{
		if( ! (Minion_Output::$_instance instanceof Minion_Output))
		{
			Minion_Output::$_instance = new Minion_Output();
		}

		return Minion_Output::$_instance;
	}

	/**
	 * A set of writers that will handle the minion output
	 * @var array
	 */
	protected $_writers = array();

	/**
	 * Add an output writer to the queue
	 *
	 * @param Minion_Output_Writer The writer to add
	 * @return Minion_Output $this
	 */
	public function add_writer(Minion_Output_Writer $writer)
	{
		$this->_writers[] = $writer;

		return $this;
	}

	/**
	 * Write the specified output to the writers
	 *
	 * @param string The output to write
	 * @param int    The type of output we're displaying
	 * @return Minion_Output
	 */
	public function write($output, $type = NULL)
	{
		if($type !== NULL)
		{
			$type = (int) $type;
		}

		foreach($this->_writers as $writer)
		{
			$writer->write($output, $type);
		}

		return $this;
	}
}
