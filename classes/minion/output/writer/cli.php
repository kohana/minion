<?php

/**
 * A minion output writer for displaying output on the command line.
 *
 * Output can be colored, there is a predefined dictionary of types => colors in 
 * $_type_colors, with each output type in Minion_Output mapped to a foreground 
 * color.
 *
 * The writer does not do output colors because it is only required in certain 
 * circumstances and would detract from the readability of some text.
 *
 * If the type of output is NULL then it will not be colored.
 *
 * Coloring can be disabled for output of all types by passing FALSE to the 
 * class' constructor
 *
 * @author Matt Button <matthew@sigswitch.com>
 */
class Minion_Output_Writer_CLI extends Minion_Output_Writer
{
	/**
	 * Whether output should be color coded
	 * @var boolean
	 */
	protected $_color_enabled = TRUE;

	/**
	 * An array of output colors for each associated type
	 * Each value is in the form array(foreground, background)
	 * @var array
	 */
	protected $_type_colors = array(
		Minion_Output::ERROR   => 'red',
		Minion_Output::WARNING => 'yellow',
		Minion_Output::INFO    => 'blue',
		Minion_Output::SUCCESS => 'green'
	);

	/**
	 * Construct the output writer
	 *
	 * @param boolean Whether output should be color coded
	 */
	public function __construct($color_enabled = NULL)
	{
		if ($color_enabled !== NULL)
		{
			$this->_color_enabled = (bool) $color_enabled;
		}
	}

	/**
	 * Write the output to the CLI
	 *
	 * @param string  The output to write
	 * @param integer The output type
	 */
	public function write($output, $type)
	{
		$foreground = NULL;
		
		if($this->_color_enabled AND isset($this->_type_colors[$type]))
		{
			$foreground = $this->_type_colors[$type];
		}

		Minion_ClI::write($output, $foreground);
	}
}
