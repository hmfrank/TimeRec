<?php

class LogEntry
{
	function  __construct($time, $active, $target_full_name)
	{
		$this->time	 = $time;
		$this->active = $active;
		$this->target_full_name = $target_full_name;
	}

	/**
	 * @var int
	 */
	public $time;

	/**
	 * @var bool
	 */
	public $active;

	/**
	 * @var string
	 */
	public $target_full_name;

	/**
	 * @param $line string
	 * @return LogEntry|bool
	 */
	public static function fromLine($line)
	{
		$chunks = explode(" ", $line);
		if ($chunks < 3)
		{
			return false;
		}

		$time = intval($chunks[0]);

		if ($chunks[1] != "+" && $chunks[1] != "-")
		{
			return false;
		}
		$active = $chunks[1] == "+";

		$start = strpos($line, " ", strpos($line, " ") + 1) + 1;
		$target_full_name = substr($line, $start);

		return new LogEntry($time, $active, $target_full_name);
	}
}