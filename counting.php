<?php

require "TimePeriod.php";

/**
 * @param $log_entries LogEntry[]
 * @param $path string[]
 * @param $period TimePeriod
 * @return int
 */
function countSeconds($log_entries, $path, $period)
{
	$seconds = 0;
	$periods = TimePeriod::getTimePeriods($log_entries, $path);
	$n_periods = count($periods);

	for ($i = 0; $i < $n_periods; $i++)
	{
		$p = $periods[$i];

		if ($p->start < $period->start)
		{
			if ($p->end > $period->start)
			{
				// count from $period->start
				if ($p->end < $period->end)
				{
					// to $p->end
					$seconds += $p->end - $period->start;
				}
				else
				{
					// to $period->end
					$seconds += $period->end - $period->start;
				}
			}
		}
		else if ($p->start < $period->end)
		{
			// count from $p->start
			if ($p->end < $period->end)
			{
				// to $p->end
				$seconds += $p->end - $p->start;
			}
			else
			{
				// to $period->end
				$seconds += $period->end - $p->start;
			}
		}
	}

	return $seconds;
}