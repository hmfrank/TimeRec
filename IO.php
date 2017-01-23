<?php

/**
 * @param $filename string
 * @param $target_full_name string
 * @param $active bool
 */
function appendLog($filename, $target_full_name, $active)
{
	$line = time() . ($active ? " + " : " - ") . $target_full_name . "\n";

	if (file_put_contents($filename, $line, FILE_APPEND) === false)
	{
		error("Can't write log file " . $filename . " !");
	}
}

/**
 * @param $filename string
 * @return string[]
 */
function readLog($filename)
{
	$lines = file($filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

	if ($lines === false)
	{
		error("ERROR: Can't read log file " . $filename . " !");
	}

	$active_set = array();
	$inactive_set = array();

	for ($i = count($lines) - 1; $i >= 0; $i--)
	{
		$line = $lines[$i];
		$parts = explode(" ", $line);

		if (count($parts) != 3)
		{
			continue;
		}

		if (!(in_array($parts[2], $active_set) || in_array($parts[2], $inactive_set)))
		{
			if ($parts[1] == "+")
			{
				array_push($active_set, $parts[2]);
			}
			else
			{
				array_push($inactive_set, $parts[2]);
			}
		}
	}

	return $active_set;
}