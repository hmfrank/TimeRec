<?php

require "LogEntry.php";

/**
 * @param $entry LogEntry
 * @param $filename string
 */
function appendLog($entry, $filename)
{
	$line = $entry->time . ($entry->active ? " + " : " - ") . implode(".", $entry->path) . "\n";

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
 * @param $entries LogEntry[]
 * @return string[][]
 */
function parseLogEntries($entries)
{
	$active_set = array();
	$inactive_set = array();

	for ($i = count($entries) - 1; $i >= 0; $i--)
	{
		$entry = $entries[$i];

		if (!(in_array($entry->path, $active_set) || in_array($entry->path, $inactive_set)))
		{
			if ($entry->active)
			{
				array_push($active_set, $entry->path);
			}
			else
			{
				array_push($inactive_set, $entry->path);
			}
		}
	}

	return $active_set;
}