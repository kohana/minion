<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Interface that all minion tasks must implement
 *
 * @package    Kohana
 * @category   Minion
 * @author     Kohana Team
 * @copyright  (c) 2009-2011 Kohana Team
 * @license    http://kohanaframework.org/license
 */
abstract class Kohana_Minion_Task {

	/**
	 * The list of options this task accepts and their default values.
	 *
	 *     protected $_options = array(
	 *         'limit' => 4,
	 *         'table' => NULL,
	 *     );
	 *
	 * @var array
	 */
	protected $_options = array();

	/**
	 * Populated with the accepted options for this task.
	 * This array is automatically populated based on $_options.
	 *
	 * @var array
	 */
	protected $_accepted_options = array();

	protected $_method = '_execute';

	/**
	 * Factory for loading minion tasks
	 *
	 * @param  array An array of command line options. It should contain the 'task' key
	 *
	 * @throws Kohana_Exception
	 *
	 * @return Minion_Task The Minion task
	 */
	public static function factory($options)
	{
		$task = Arr::get($options, 'task');
		unset($options['task']);

		// If we didn't get a valid task, generate the help
		if ( ! is_string($task))
		{
			$task = 'help';
		}

		$class = Minion_Util::convert_task_to_class_name($task);

		if ( ! in_array('Minion_Task', class_parents($class)))
		{
			throw new Kohana_Exception(
				"Task ':task' is not a valid minion task",
				array(':task' => get_class($task))
			);
		}

		$class = new $class;
		$class->set_options($options);

		// Show the help page for this task if requested
		if (array_key_exists('help', $options))
		{
			$class->_method = '_help';
		}

		return $class;
	}

	protected function __construct()
	{
		// Populate $_accepted_options based on keys from $_options
		$this->_accepted_options = array_keys($this->_options);
	}

	/**
	 * The file that get's passes to Validation::errors() when validation fails
	 * @var string|NULL
	 */
	protected $_errors_file = 'validation';

	/**
	 * Gets the task name for the task
	 *
	 * @return string
	 */
	public function __toString()
	{
		static $task_name = NULL;

		if ($task_name === NULL)
		{
			$task_name = Minion_Util::convert_class_to_task($this);
		}

		return $task_name;
	}

	/**
	 * Sets options for this task
	 *
	 * $param  array  the array of options to set
	 * @return this
	 */
	public function set_options(array $options)
	{
		foreach ($options as $key => $value)
		{
			$this->_options[$key] = $value;
		}

		return $this;
	}

	/**
	 * Get the options that were passed into this task with their defaults
	 *
	 * @return array
	 */
	public function get_options()
	{
		return (array) $this->_options;
	}

	/**
	 * Get a set of options that this task can accept
	 *
	 * @return array
	 */
	public function get_accepted_options()
	{
		return (array) $this->_accepted_options;
	}

	/**
	 * Adds any validation rules/labels for validating _options
	 *
	 *     public function build_validation(Validation $validation)
	 *     {
	 *         return parent::build_validation($validation)
	 *             ->rule('paramname', 'not_empty'); // Require this param
	 *     }
	 *
	 * @param  Validation   the validation object to add rules to
	 *
	 * @return Validation
	 */
	public function build_validation(Validation $validation)
	{
		// Add a rule to each key making sure it's in the task
		foreach ($validation->as_array() as $key => $value)
		{
			$validation->rule($key, array($this, 'valid_option'), array(':validation', ':field'));
		}

		return $validation;
	}

	/**
	 * Returns $_errors_file
	 *
	 * @return string
	 */
	public function get_errors_file()
	{
		return $this->_errors_file;
	}

	/**
	 * Execute the task with the specified set of options
	 *
	 * @return null
	 */
	public function execute()
	{
		$options = $this->get_options();

		// Validate $options
		$validation = Validation::factory($options);
		$validation = $this->build_validation($validation);

		if ( $this->_method != '_help' AND ! $validation->check())
		{
			echo View::factory('minion/error/validation')
				->set('task', Minion_Util::convert_class_to_task($this))
				->set('errors', $validation->errors($this->get_errors_file()));
		}
		else
		{
			// Finally, run the task
			$method = $this->_method;
			echo $this->{$method}($options);
		}
	}

	abstract protected function _execute(array $params);

	/**
	 * Outputs help for this task
	 *
	 * @return null
	 */
	protected function _help(array $params)
	{
		$tasks = Minion_Util::compile_task_list(Kohana::list_files('classes/task'));

		$inspector = new ReflectionClass($this);

		list($description, $tags) = Minion_Util::parse_doccomment($inspector->getDocComment());

		$view = View::factory('minion/help/task')
			->set('description', $description)
			->set('tags', (array) $tags)
			->set('task', Minion_Util::convert_class_to_task($this));

		echo $view;
	}


	public function valid_option(Validation $validation, $option)
	{
		if ( ! in_array($option, $this->_accepted_options))
		{
			$validation->error($key, 'minion_option');
		}
	}
}
