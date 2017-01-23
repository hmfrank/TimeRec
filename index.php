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

foreach ($active_set as $active)
{
	$tree->setActive($active);
}
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