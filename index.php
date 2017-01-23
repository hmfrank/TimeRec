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
$active_set = readLog("active.log");

$start = $_GET["start"];
$stop = $_GET["stop"];

if ($start != null || $stop != null)
{
	if ($start != null && $tree->isLeaf($start) && !in_array($start, $active_set))
	{
		appendLog("active.log", $start, true);
	}

	if ($stop != null && $tree->isLeaf($stop) && in_array($stop, $active_set))
	{
		appendLog("active.log", $stop, false);
	}

	$active_set = readLog("active.log");
}

$tree->setActives($active_set);
?>

<h1>Time Recording</h1>

<?php $tree->print("", 0, true); ?>

<style>
	.active {
		color: tomato;
	}
</style>

</body>
</html>