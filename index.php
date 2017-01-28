<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<title>Time Recording</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

</head>
<body>

<?php
require "Forest.php";
require "io.php";

$forest = Forest::fromConfFile("conf.json");
$log_entries = readLog("active.log");
$active_set = parseLogEntries($log_entries);

$start = isset($_GET["start"]) ? $_GET["start"] : null;
$stop = isset($_GET["stop"]) ? $_GET["stop"] : null;

if ($start != null || $stop != null)
{
	$start = explode(".", urldecode($start));
	$stop = explode(".", urldecode($stop));

	if ($start != null && $forest->isLeaf($start) && !in_array($start, $active_set))
	{
		$log_entry = new LogEntry(time(), true, $start);
		array_push($log_entries, $log_entry);
		appendLog($log_entry, "active.log");
	}

	if ($stop != null && $forest->isLeaf($stop) && in_array($stop, $active_set))
	{
		$log_entry = new LogEntry(time(), false, $stop);
		array_push($log_entries, $log_entry);
		appendLog($log_entry, "active.log");
	}

	$active_set = parseLogEntries($log_entries);
}

foreach ($active_set as $path)
{
	$forest->setActive($path);
}
?>

<h1>Time Recording</h1>

<?php $forest->show($log_entries); ?>

<style>
	.active {
		background-color: #ffe9b9;
	}
</style>

</body>
</html>