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
 * Prints `$n` tab characters.
 *
 * @param $n int
 */
function indent($n)
{
	for ($i = 0; $i < $n; $i++)
	{
		echo "\t";
	}
}