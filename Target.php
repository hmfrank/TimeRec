<?php

require "IO.php";
require "Utils.php";

class Target
{
	/** @var string */
	private $name;

	/** @var Target[] */
	private $children;

	/** @var  bool */
	private $active;

	/**
	 * @param $name string
	 */
	function __construct($name)
	{
		$this->name = $name;
		$this->children = array();
		$this->active = false;
	}

	/**
	 * @return bool
	 */
	function isLeaf()
	{
		return count($this->children) == 0;
	}


	/**
	 * @param $parent_full_name string
	 * @param $i int
	 * @param $root bool
	 */
	function print($parent_full_name, $i, $root)
	{
		$full_name = $root ? $this->name : $parent_full_name . "." . $this->name;

		if (!$root)
		{
			indent($i);
			echo "<span " . ($this->active ? "class=\"active\"" : "") . ">" . htmlentities($this->name) . "</span>\n";
		}

		if ($this->isLeaf() && !$root)
		{
			indent($i);
			echo "<a href=\".?" . ($this->active ? "stop" : "start") . "=" . urlencode($full_name) . "\">toggle</a>\n";
		}
		else
		{
			indent($i);
			echo "<ul>\n";

			foreach ($this->children as $child)
			{
				indent($i + 1);
				echo "<li>\n";

				$child->print($full_name, $i + 2, false);

				indent($i + 1);
				echo "</li>\n";
			}

			indent($i);
			echo "</ul>\n";
		}
	}

	function resetActive()
	{
		$this->active = false;

		foreach ($this->children as $child)
		{
			$child->resetActive();
		}
	}

	/**
	 * @param $full_name string
	 * @return bool
	 */
	function setActive($full_name)
	{
		$pos = strpos($full_name, ".");
		$pos = $pos === false ? $pos = strlen($full_name) : $pos;

		$first_part = substr($full_name, 0, $pos);
		$last_part = substr($full_name, $pos + 1);

		if ($first_part == $this->name)
		{
			if ($this->isLeaf())
			{
				$this->active = true;
			}
			else
			{
				foreach ($this->children as $child)
				{
					if ($child->setActive($last_part))
					{
						$this->active = true;
						break;
					}
				}
			}
		}

		return $this->active;
	}

	/**
	 * @param $filename string
	 * @return Target
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

		return Target::fromJson("", $json);
	}

	/**
	 * @param $name string
	 * @param $json array
	 * @return Target
	 */
	static function fromJson($name, $json)
	{
		$result = new Target($name);

		foreach ($json as $key => $value)
		{
			array_push($result->children, Target::fromJson($key, $value));
		}

		return $result;
	}
}
