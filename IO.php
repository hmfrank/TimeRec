<?php

require "LogEntry.php";

/**
 * @param $entry LogEntry
 * @param $filename string
 */
function appendLog($entry, $filename)
{
	$line = $entry->time . ($entry->active ? " + " : " - ") . $entry->target_full_name . "\n";

	if (file_put_contents($filename, $line, FILE_APPEND) === false)
	{
		error("Can't write log file " . $filename . " !");
	}
}

/**
 * @param $filename string
 * @return LogEntry[]
 */
function readLog($filename)
{
	$lines = file($filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

	if ($lines === false)
	{
		error("ERROR: Can't read log file " . $filename . " !");
	}

	$result = array();

	foreach ($lines as $line)
	{
		$log_entry = LogEntry::fromLine($line);

		if ($log_entry !== false)
		{
			array_push($result, $log_entry);
		}
	}

	return $result;
}

/**
 * @param $filename string
 * @return string[]
 */
function parseLogEntries($entries)
{
	$active_set = array();
	$inactive_set = array();

	for ($i = count($entries) - 1; $i >= 0; $i--)
	{
		$entry = $entries[$i];

		if (!(in_array($entry->target_full_name, $active_set) || in_array($entry->target_full_name, $inactive_set)))
		{
			if ($entry->active)
			{
				array_push($active_set, $entry->target_full_name);
			}
			else
			{
				array_push($inactive_set, $entry->target_full_name);
			}
		}
	}

	return $active_set;
}