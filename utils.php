<?php

/**
 * Sets the HTTP response code to 500 (internal server error), prints the given error message (in red), closes the body
 * and html tags and terminates the script execution.
 *
 * @param $message string
 */
function error($message)
{
	http_response_code(500);
	die("<p style=\"color: red;\">" . htmlentities($message) . "</p></body></html>");
}

/**
 * @param $seconds int
 * @return string
 */
function formatTime($seconds)
{
	$s = $seconds % 60;
	$minutes = floor($seconds / 60);
	$m = $minutes % 60;
	$h = floor($minutes/ 60);

	$s = ($s < 10 ? "0" : "") . $s;
	$m = ($m < 10 ? "0" : "") . $m;

	return join(":", array($h, $m, $s));
}