<?php

require "TimePeriod.php";

/**
 * @param $log_entries LogEntry[]
 * @param $path string[]
 * @param $start_time int
 * @param $end_time int
 * @return int
 */
function countSeconds($log_entries, $path, $start_time, $end_time)
{
	$seconds = 0;
	$periods = TimePeriod::getTimePeriods($log_entries, $path);
	$n_periods = count($periods);

	for ($i = 0; $i < $n_periods; $i++)
	{
		$p = $periods[$i];

		if ($p->start < $start_time)
		{
			if ($p->end > $start_time)
			{
				// count from $start_time
				if ($p->end < $end_time)
				{
					// to $p->end
					$seconds += $p->end - $start_time;
				}
				else
				{
					// to $end_time
					$seconds += $end_time - $start_time;
				}
			}
		}
		else if ($p->start < $end_time)
		{
			// count from $p->start
			if ($p->end < $end_time)
			{
				// to $p->end
				$seconds += $p->end - $p->start;
			}
			else
			{
				// to $end_time
				$seconds += $end_time - $p->start;
			}
		}
	}

	return $seconds;
}