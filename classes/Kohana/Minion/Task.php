<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Abstract minion task, parent class for all tasks.
 *
 * @package    Kohana/Minion
 * @category   Task
 * @author     Kohana Team
 * @copyright  (c) 2009-2014 Kohana Team
 * @license    http://kohanaframework.org/license
 */
abstract class Kohana_Minion_Task implements Kohana_Task {

	/**
	 * @var string Separate different levels of tasks.
	 */
	public static $separator = ':';

	/**
	 * @var array The list of options this task accepts and their default values.
	 */
	protected $_options = array();

	/**
	 * @var array Accepted task options, auto populated based on [Minion_Task::$_options].
	 */
	protected $_accepted_options = array();

	/**
	 * @var string Default name of task method to execute.
	 */
	protected $_method = '_execute';

	/**
	 * @var string The file that get's passes to [Validation::errors()] when validation fails.
	 */
	protected $_errors_file = 'minion/validation';
	
	/**
	 *
	 * @var Mininon_CLI_Output 
	 */
	protected $output;

	/**
	 * Converts a task to class name.
	 * 
	 *     echo Minion_Task::convert_task_to_class_name('db:migrate');
	 *     // Result: 'Task_Db_Migrate'
	 * 
	 * @param  string $task Task name
	 * @return string Class name
	 */
	public static function convert_task_to_class_name($task)
	{
		$task = trim($task, Minion_Task::$separator);

		if ( ! empty($task))
		{
			// 'db:migrate' -> 'db migrate'
			$task = str_replace(Minion_Task::$separator, ' ', $task);
			// 'db migrate' -> 'Db Migrate' -> 'Db_Migrate'
			$task = str_replace(' ', '_', ucwords($task));
			// 'Db_Migrate' -> 'Task_Db_Migrate'
			$task = 'Task_'.$task;
		}

		return $task;
	}

	/**
	 * Converts a class\object to task name.
	 * 
	 *     echo Minion_Task::convert_class_to_task('Task_Db_Migrate');
	 *     // Result: 'db:migrate'
	 * 
	 * @param  string|object $class Class name or instance of [Minion_Task].
	 * @return string Task name
	 */
	public static function convert_class_to_task($class)
	{
		if (is_object($class))
		{
			$class = get_class($class);
		}

		if ( ! empty($class))
		{
			// 'Task_Db_Migrate' -> 'Db_Migrate'
			$class = substr($class, 5);
			// 'Db_Migrate' -> 'db_migrate' -> 'db:migrate'
			$class = str_replace('_', Minion_Task::$separator, strtolower($class));
		}

		return $class;
	}

	/**
	 * 
	 * @param CLI_Stream_STDOUT $output
	 * @return Minion_Task
	 */
	public function set_output(CLI_Stream_STDOUT $output = NULL)
	{
		if(is_a($output, 'CLI_Output'))
		{
			$this->output = $output;
		}
		else
		{
			$this->output = CLI::factory('CLImate');
		}
		
		return $this;
	}

	/**
	 * Factory for create minion tasks.
	 * 
	 *     $task = Minion_Task::factory('db:migrate');
	 * 
	 * @param  string             $name    Name of the Task to create
	 * @param  CLI_Stream_STDOUT  $output  CLI output interface
	 * @return Instance of [Minion_Task]
	 * @throws Minion_Task_Exception
	 */
	public static function factory($name, CLI_Output $output = NULL)
	{
		$name = Minion_Task::convert_task_to_class_name($name);
		if ( ! class_exists($name))
		{
			throw new Minion_Task_Exception(
				'Task class `:class` not exists', 
				array(':class' => $name)
			);
		}
		elseif ( ! is_subclass_of($name, 'Minion_Task'))
		{
			throw new Minion_Task_Exception(
				'Class `:class` is not a valid minion task', 
				array(':class' => $name)
			);
		}
		
		// Load the task using reflection
		$class = new ReflectionClass($name);

		if ($class->isAbstract())
		{
			throw new Kohana_Exception(
				'Cannot create instances of abstract :task',
				array(':task' => $name)
			);
		}

		// Create a new instance of the task
		$task = new $name;
		
		// Set the Stdout interface
		$task->set_output();
		
		return $task;
	}

	/**
	 * Populate [Minion_Task::$_accepted_options] based on keys from [Minion_Task::$_options].
	 *
	 * @return void
	 */
	protected function __construct()
	{
		$this->_accepted_options = array_keys($this->_options);
	}

	/**
	 * Gets the task name for the task.
	 *
	 * @return string
	 */
	public function __toString()
	{
		return Minion_Task::convert_class_to_task($this);
	}

	/**
	 * Get the options that were passed into this task with their defaults.
	 *
	 * @return array
	 */
	public function get_options()
	{
		return (array) $this->_options;
	}

	/**
	 * Get a set of options that this task can accept.
	 *
	 * @return array
	 */
	public function get_accepted_options()
	{
		return (array) $this->_accepted_options;
	}

	/**
	 * Validate option.
	 *
	 * @param  object $validation The [Validation] object
	 * @param  string $option     The option name
	 * @return void
	 */
	public function valid_option(Validation $validation, $option)
	{
		if ( ! in_array($option, $this->_accepted_options))
		{
			$validation->error($option, 'minion_option');
		}
	}

	/**
	 * Adds any validation rules and labels for validating [Minion_Task::$_options].
	 *
	 *     public function build_validation(Validation $validation)
	 *     {
	 *         return parent::build_validation($validation)
	 *             ->label('option1', 'Option one')
	 *             ->rule('option1', 'not_empty');
	 *     }
	 *
	 * @param  object $validation The [Validation] to add rules to
	 * @return Validation object
	 */
	public function build_validation(Validation $validation)
	{
		// Add a rule to each key making sure it's in the task
		foreach ($validation->data() as $key => $value)
		{
			$validation->rule(
				$key, 
				array($this, 'valid_option'), 
				array(':validation', ':field')
			);
		}

		return $validation;
	}

	/**
	 * Returns [Minion_Task::$_errors_file].
	 *
	 * @return string
	 */
	public function get_errors_file()
	{
		return $this->_errors_file;
	}

	/**
	 * Show the help page for this task if requested
	 * 
	 * @return array
	 */
	public function check_help($options)
	{
		if (array_key_exists('help', $options))
		{
			$this->_method = '_help';

			unset($options['help']);
		}
		
		return $options;
	}
	
	/**
	 * 
	 * @return string
	 */
	public function method()
	{
		return $this->_method;
	}
	
	/**
	 * 
	 * @return bool
	 */
	public function is_help()
	{
		return (bool)($this->method() == '_help');
	}	
	
	/**
	 * Execute the task.
	 * 
	 * @param  array $params Input values
	 * @return void
	 * @uses   Validation::factory
	 * @uses   View::factory
	 * @uses   CLI_Output::write
	 */
	public function execute(array $params)
	{
		// Merge runtime params with defaults
		$options = array_merge($this->_options, $params);

		// Run the help method?
		$options = $this->check_help($options);

		// Validate options
		$validation = Validation::factory($options);
		$validation = $this->build_validation($validation);

		if ( ! $this->is_help() AND ! $validation->check())
		{
			// Display error
			$view = View::factory('minion/error/validation')
				->set('task', Minion_Task::convert_class_to_task($this))
				->set('errors', $validation->errors($this->_errors_file));

			$this->output->write($view);

			return CLI_Command::FAIL;
		}
		else
		{
			try
			{
				// Finally, run the task
				$this->{$this->_method}($options);
			} 
			catch (Exception $e) 
			{
				echo $e->getMessage();

				return CLI_Command::FAIL;
			}
		}
		
		return CLI_Command::SUCCESS;
	}

	/**
	 * Task action.
	 * 
	 * [!!] Override this method in current task.
	 * 
	 * @param  array $params The validated parameters passed from CLI
	 * @return void
	 */
	abstract protected function _execute(array $params);

	/**
	 * Outputs help for this task.
	 * 
	 * @return void
	 * @uses   View::factory
	 * @uses   CLI_Output::write
	 */
	protected function _help()
	{
		$inspector = new ReflectionClass($this);

		list($description, $tags) = $this->_parse_doccomment($inspector->getDocComment());

		$view = View::factory('minion/help/task')
			->set('description', $description)
			->set('tags', (array) $tags)
			->set('options', $this->_options)
			->set('task', Minion_Task::convert_class_to_task($this));

		$this->output->write($view);
	}

	/**
	 * Parses a doccomment, extracting both the comment and any tags associated.
	 *
	 * @param  string $comment The comment to parse
	 * @return Array contained comment and tags
	 * @uses   Arr::get
	 */
	protected function _parse_doccomment($comment)
	{
		// Normalize all new lines to '\n'
		$comment = str_replace(array("\r\n", "\n"), "\n", $comment);
		// Remove the phpdoc open\close tags and split
		$comment = array_slice(explode("\n", $comment), 1, -1);

		// Tag content
		$tags = array();

		foreach ($comment as $i => $line)
		{
			// Remove all leading whitespace
			$line = preg_replace('/^\s*\* ?/m', '', $line);

			// Search this line for a tag
			if (preg_match('/^@(\S+)(?:\s*(.+))?$/', $line, $matches))
			{
				$tags[$matches[1]] = isset($matches[2]) ? $matches[2] : '';
				unset($comment[$i]);
			}
			else
			{
				$comment[$i] = $line;
			}
		}

		$comment = trim(implode(PHP_EOL, $comment));

		return array($comment, $tags);
	}

	/**
	 * Compiles a list of available tasks from a directory structure.
	 *
	 * @param  array  $files  Directory structure of tasks
	 * @param  string $prefix Task prefix
	 * @return array  Compiled tasks
	 */
	protected function _compile_task_list(array $files, $prefix = '')
	{
		$output = array();

		foreach ($files as $file => $path)
		{
			$file = substr($file, strrpos($file, DIRECTORY_SEPARATOR) + 1);

			if (is_array($path) AND count($path) > 0)
			{
				$tasks = $this->_compile_task_list($path, $prefix.$file.Minion_Task::$separator);

				if ($tasks)
				{
					$output = array_merge($output, $tasks);
				}
			}
			else
			{
				$output[] = strtolower($prefix.substr($file, 0, -strlen(EXT)));
			}
		}

		return $output;
	}

}
