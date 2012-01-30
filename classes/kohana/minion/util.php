<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Minion utility class
 *
 * @package    Kohana
 * @category   Minion
 * @author     Kohana Team
 * @copyright  (c) 2009-2011 Kohana Team
 * @license    http://kohanaframework.org/license
 */
class Kohana_Minion_Util
{
	/**
	 * The separator used to separate different levels of tasks
	 * @var string
	 */
	public static $task_separator = ':';

	/**
	 * Parses a doccomment, extracting both the comment and any tags associated
	 *
	 * Based on the code in Kodoc::parse()
	 *
	 * @param string The comment to parse
	 * @return array First element is the comment, second is an array of tags
	 */
	public static function parse_doccomment($comment)
	{
		// Normalize all new lines to \n
		$comment = str_replace(array("\r\n", "\n"), "\n", $comment);

		// Remove the phpdoc open/close tags and split
		$comment = array_slice(explode("\n", $comment), 1, -1);

		// Tag content
		$tags        = array();

		foreach ($comment as $i => $line)
		{
			// Remove all leading whitespace
			$line = preg_replace('/^\s*\* ?/m', '', $line);

			// Search this line for a tag
			if (preg_match('/^@(\S+)(?:\s*(.+))?$/', $line, $matches))
			{
				// This is a tag line
				unset($comment[$i]);

				$name = $matches[1];
				$text = isset($matches[2]) ? $matches[2] : '';

				$tags[$name] = $text;
			}
			else
			{
				$comment[$i] = (string) $line;
			}
		}

		$comment = trim(implode("\n", $comment));

		return array($comment, $tags);
	}

	/**
	 * Compiles a list of available tasks from a directory structure
	 *
	 * @param  array Directory structure of tasks
	 * @return array Compiled tasks
	 */
	public static function compile_task_list(array $files, $prefix = '')
	{
		$output = array();

		foreach ($files as $file => $path)
		{
			$file = substr($file, strrpos($file, DIRECTORY_SEPARATOR) + 1);

			if (is_array($path) AND count($path))
			{
				$task = Minion_Util::compile_task_list($path, $prefix.$file.Minion_Util::$task_separator);

				if ($task)
				{
					$output = array_merge($output, $task);
				}
			}
			else
			{
				$output[] = strtolower($prefix.substr($file, 0, -strlen(EXT)));
			}
		}

		return $output;
	}

	/**
	 * Converts a task (e.g. db:migrate to a class name)
	 *
	 * @param string  Task name
	 * @return string Class name
	 */
	public static function convert_task_to_class_name($task)
	{
		$task = trim($task);

		if (empty($task))
			return '';

		return 'Task_'.implode('_', array_map('ucfirst', explode(Minion_Util::$task_separator, $task)));
	}

	/**
	 * Gets the task name of a task class / task object
	 *
	 * @param  string|Minion_Task The task class / object
	 * @return string             The task name
	 */
	public static function convert_class_to_task($class)
	{
		if (is_object($class))
		{
			$class = get_class($class);
		}

		return strtolower(str_replace('_', Minion_Util::$task_separator, substr($class, 12)));
	}
}
