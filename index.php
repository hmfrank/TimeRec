<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<title>Time Recording</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

</head>
<body>

<?php
require "Target.php";

$tree = Target::fromConfFile("conf.json");
$log_entries = readLog("active.log");
$active_set = parseLogEntries($log_entries);

$start = $_GET["start"];
$stop = $_GET["stop"];

if ($start != null || $stop != null)
{
	if ($start != null && $tree->isLeaf($start) && !in_array($start, $active_set))
	{
		$log_entry = new LogEntry(time(), true, $start);
		array_push($log_entries, $log_entry);
		appendLog($log_entry, "active.log");
	}

	if ($stop != null && $tree->isLeaf($stop) && in_array($stop, $active_set))
	{
		$log_entry = new LogEntry(time(), false, $stop);
		array_push($log_entries, $log_entry);
		appendLog($log_entry, "active.log");
	}

	$active_set = parseLogEntries($log_entries);
}

$tree->setActives($active_set);
?>

<h1>Time Recording</h1>

<?php $tree->show("", 0, true); ?>

<style>
	.active {
		color: tomato;
	}
</style>

</body>
</html>