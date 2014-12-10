<?php

/**
 * Provides standard methods for classes to get and set their I/O streams
 */
Trait STDIO {

	/**
	 *
	 * @var CLI_Options
	 */
	protected $options;

	/**
	 *
	 * @var CLI_Stream_STDIN
	 */
	protected $input;

	/**
	 *
	 * @var CLI_Stream_STDOUT
	 */
	protected $output;


	/**
	 *
	 * @return CLI_Options
	 */
	public function get_options()
	{
		return $this->options;
	}

	/**
	 *
	 * @return CLI_Stream_STDIN
	 */
	public function get_input()
	{
		return $this->input;
	}

	/**
	 *
	 * @return CLI_Stream_STDOUT
	 */
	public function get_output()
	{
		return $this->output;
	}



	/**
	 *
	 * @param CLI_Options $options
	 * @return mixed
	 */
	public function set_options(CLI_Options $options)
	{
		if (is_a($options, 'CLI_Options'))
		{
			$this->options = $options;
		}

		return $this;
	}

	/**
	 *
	 * @param CLI_Stream_STDOUT $input
	 * @return mixed
	 */
	public function set_input(CLI_Stream_STDIN $input)
	{
		if (is_a($input, 'CLI_Stream_STDIN'))
		{
			$this->input = $input;
		}

		return $this;
	}

	/**
	 *
	 * @param CLI_Stream_STDOUT $output
	 * @return mixed
	 */
	public function set_output(CLI_Stream_STDOUT $output)
	{
		if (is_a($output, 'CLI_Stream_STDOUT'))
		{
			$this->output = $output;
		}

		return $this;
	}
}
