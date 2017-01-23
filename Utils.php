<?php

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