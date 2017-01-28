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

			if ($entry->path == $path)
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

		return $result;
	}
}