<?php

class TimePeriod
{
	/** @var int */
	public $start;

	/** @var int */
	public $end;

	/**
	 * TimePeriod constructor.
	 * @param $start int
	 * @param $end int
	 */
	function __construct($start, $end)
	{
		$this->start = $start;
		$this->end = $end;
	}

	static function getAllTime()
	{
		return new TimePeriod(PHP_INT_MIN, PHP_INT_MAX);
	}

	static function getCurrentWeek()
	{
		$timestamp = time();
		$now = getdate($timestamp);

		$seconds_into_week = $now["seconds"] + 60 * $now["minutes"] + 3600 * $now["hours"] + 3600 * 24 * $now["wday"];

		$start = $timestamp - $seconds_into_week;
		$end = $start + 3600 * 24 * 7;

		return new TimePeriod($start, $end);
	}

	static function getLastWeek()
	{
		$curent_week = TimePeriod::getCurrentWeek();
		return new TimePeriod($curent_week->start - 3600 * 24 * 7, $curent_week->start);
	}

	static function getToday()
	{
		$timestamp = time();
		$now = getdate($timestamp);

		$seconds_into_day = $now["seconds"] + 60 * $now["minutes"] + 3600 * $now["hours"];

		$start = $timestamp - $seconds_into_day;
		$end = $start + 3600 * 24;

		return new TimePeriod($start, $end);
	}

	/**
	 * @param $log_entries LogEntry[]
	 * @param $path string[]
	 * @return TimePeriod[]
	 */
	static function getTimePeriods($log_entries, $path)
	{
		$result = array();

		$start = null;

		$n_log_entries = count($log_entries);

		for ($i = 0; $i < $n_log_entries; $i++)
		{
			$entry = $log_entries[$i];

			if ($path == $entry->path)
			{
				if ($entry->active)
				{
					if ($start == null)
					{
						$start = $entry->time;
					}
				}
				else
				{
					if ($start != null)
					{
						$end = $entry->time;
						array_push($result, new TimePeriod($start, $end));

						$start = null;
					}
				}
			}
		}

		if ($start != null)
		{
			array_push($result, new TimePeriod($start, time()));
		}

		return $result;
	}
}