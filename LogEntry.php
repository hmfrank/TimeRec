<?php

class LogEntry
{
	/**
	 * @var int
	 */
	public $time;

	/**
	 * @var bool
	 */
	public $active;

	/**
	 * @var string[]
	 */
	public $path;

	function  __construct($time, $active, $path)
	{
		$this->time	 = $time;
		$this->active = $active;
		$this->path = $path;
	}

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
		$path = substr($line, $start);
		$path = explode(".", $path);

		return new LogEntry($time, $active, $path);
	}
}