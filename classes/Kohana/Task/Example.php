<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Example task
 *
 * @package    Kohana/Minion
 * @category   Task
 * @author     Kohana Team
 * @copyright  (c) 2009-2014 Kohana Team
 * @license    http://kohanaframework.org/license
 */
abstract class Kohana_Task_Example extends Minion_Task {
	
	protected $_options = [
	    'foo'  => 'bar',
	    'opt' => NULL,
	];

	/**
	 * Example Task.
	 *
	 * 
	 * @return void
	 */
	protected function _execute(array $params)
	{
		$data = [
		    [
		      'PHP',
		      phpversion(),
		    ],
		    [
		      'Date',
		      date(DATE_RSS),
		    ],
		    [
		      'Params',
		      implode(' ', $params)
		    ]
		];

		$this->output->whiteTable($data);
		
		$progress = $this->output->yellowProgress()->total(100);

		for ($i = 0; $i <= 100; $i++)
		{
			$progress->current($i);

			// Simulate something happening
			usleep(40000);
		}
		
		$this->output->error('Ruh roh.');
		$this->output->comment('Just so you know.');
		$this->output->whisper('Not so important, just a heads up.');
		$this->output->shout('This. This is important.');
		$this->output->info('Nothing fancy here. Just some info.');		
	}
}
