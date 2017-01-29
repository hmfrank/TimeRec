<?php

require "counting.php";
require "utils.php";

class Forest
{
	/** @var Node[] */
	public $roots;

	function __construct()
	{
		$this->roots = array();
	}

	/**
	 * @param $log_entries LogEntry[]
	 */
	function calculateDurations($log_entries)
	{
		foreach ($this->roots as $root)
		{
			$root->calculateDurations($log_entries, array($root->name));
		}
	}

	/**
	 * @param $path string[]
	 * @return bool
	 */
	function isLeaf($path)
	{
		if (count($path) == 0)
		{
			return false;
		}

		foreach ($this->roots as $root)
		{
			if ($root->name == $path[0])
			{
				return $root->isLeaf(array_slice($path, 1));
			}
		}

		return false;
	}

	/**
	 * @param $path string[]
	 */
	function setActive($path)
	{
		if (count($path) == 0)
		{
			return;
		}

		foreach ($this->roots as $root)
		{
			if ($root->name == $path[0])
			{
				$root->setActive(array_slice($path, 1));
			}
		}
	}

	function setInactive()
	{
		foreach ($this->roots as $root)
		{
			$root->setInactive();
		}
	}

	function show()
	{
		echo "<table>\n";

		echo "\t<tr>\n";
		echo "\t\t<th></th>\n";
		echo "\t\t<th>Total</th>\n";
		echo "\t\t<th>Last Week</th>\n";
		echo "\t\t<th>Current Week</th>\n";
		echo "\t\t<th>Today</th>\n";
		echo "\t</tr>\n";

		foreach ($this->roots as $root)
		{
			$root->show(array($root->name));
		}
		echo "</table>\n";
	}

	/**
	 * @param $filename string
	 * @return Forest
	 */
	static function fromConfFile($filename)
	{
		$string = file_get_contents($filename);
		if ($string === false)
		{
			error("ERROR: Can't read config file " . $filename . " !");
		}

		$json = json_decode($string, true);
		if ($json === null)
		{
			error("ERROR: Invalid JSON config in " . $filename . " !");
		}

		return Forest::fromJsonObject($json);
	}

	/**
	 * @param $json array
	 * @return Forest
	 */
	static function fromJsonObject($json)
	{
		$result = new Forest();

		foreach ($json as $key => $value)
		{
			array_push($result->roots, Node::fromJsonObject($key, $value));
		}

		return $result;
	}
}

class Node
{
	/** @var string */
	public $name;

	/** @var bool */
	public $active;

	/** @var Node[] */
	public $children;

	/** @var int[] */
	private $durations;

	/**
	 * @param $name string
	 */
	function __construct($name)
	{
		$this->name = $name;
		$this->children = array();
		$this->active = false;
		$this->durations = array();
	}

	/**
	 * @param $log_entries LogEntry[]
	 * @param $absolute_path string[]
	 * @return int[]
	 */
	function calculateDurations($log_entries, $absolute_path)
	{
		$this->durations = array(0, 0, 0, 0);

		if ($this->isLeaf(array()))
		{
			$this->durations[0] = countSeconds($log_entries, $absolute_path, TimePeriod::getAllTime());
			$this->durations[1] = countSeconds($log_entries, $absolute_path, TimePeriod::getLastWeek());
			$this->durations[2] = countSeconds($log_entries, $absolute_path, TimePeriod::getCurrentWeek());
			$this->durations[3] = countSeconds($log_entries, $absolute_path, TimePeriod::getToday());
		}
		else
		{
			foreach ($this->children as $child)
			{
				array_push($absolute_path, $child->name);
				$child_durations = $child->calculateDurations($log_entries, $absolute_path);
				array_pop($absolute_path);

				for ($i = count($child_durations) - 1; $i >= 0; $i--)
				{
					$this->durations[$i] += $child_durations[$i];
				}
			}
		}

		return $this->durations;
	}

	/**
	 * @param $relative_path string[]
	 * @return bool
	 */
	function isLeaf($relative_path)
	{
		if (count($relative_path) == 0)
		{
			return count($this->children) == 0;
		}

		foreach ($this->children as $child)
		{
			if ($child->name == $relative_path[0])
			{
				return $child->isLeaf(array_slice($relative_path, 1));
			}
		}

		return false;
	}

	/**
	 * @param $relative_path string[]
	 * @return bool
	 */
	function setActive($relative_path)
	{
		if ($this->isLeaf(array()))
		{
			$this->active = true;
		}
		else if (count($relative_path) > 0)
		{
			foreach ($this->children as $child)
			{
				if ($child->name == $relative_path[0])
				{
					if ($child->setActive(array_slice($relative_path, 1)))
					{
						$this->active = true;
					}
				}
			}
		}

		return $this->active;
	}

	function setInactive()
	{
		$this->active = false;

		foreach ($this->children as $child)
		{
			$child->setInactive();
		}
	}

	/**
	 * @param $absolute_path string[]
	 */
	function show($absolute_path)
	{
		$indent = (count($absolute_path) - 1) * 2;

		echo "\t<tr" . ($this->active ? " class=\"active\"" : "") . ">\n";

		echo "\t\t<td style=\"text-indent: " . $indent . "em\">";
		if ($this->isLeaf(array()))
		{
			echo "<a href=\"./?" . ($this->active ? "stop" : "start") . "=" . implode(".", $absolute_path) . "\">";
			echo htmlentities($this->name) . "</a>";
		}
		else
		{
			echo htmlentities($this->name);
		}
		echo "</td>\n";

		$n_durations = count($this->durations);
		for ($i = 0; $i < $n_durations; $i++)
		{
			echo "\t\t<td>" . formatTime($this->durations[$i]) . "</td>\n";
		}

		echo "\t</tr>\n";

		foreach ($this->children as $child)
		{
			array_push($absolute_path, $child->name);
			$child->show($absolute_path);
			array_pop($absolute_path);
		}
	}

	/**
	 * @param $name string
	 * @param $json array
	 * @return Node
	 */
	static function fromJsonObject($name, $json)
	{
		$result = new Node($name);

		foreach ($json as $key => $value)
		{
			array_push($result->children, Node::fromJsonObject($key, $value));
		}

		return $result;
	}
}