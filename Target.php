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
	 * @param $full_name  string|null
	 * @return bool
	 */
	function isLeaf($full_name = null)
	{
		if ($full_name === null)
		{
			return count($this->children) == 0;
		}
		else
		{
			$split = Target::splitAtFirstPeriod($full_name);

			if ($split[0] == $this->name)
			{
				if ($this->isLeaf())
				{
					return true;
				}

				foreach ($this->children as $child)
				{
					if ($child->isLeaf($split[1]))
					{
						return true;
					}
				}
			}

			return false;
		}
	}


	/**
	 * @param $parent_full_name string
	 * @param $i int
	 * @param $root bool
	 */
	function show($parent_full_name, $i, $root)
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

				$child->show($full_name, $i + 2, false);

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
		$split = Target::splitAtFirstPeriod($full_name);

		if ($split[0] == $this->name)
		{
			if ($this->isLeaf())
			{
				$this->active = true;
			}
			else
			{
				foreach ($this->children as $child)
				{
					if ($child->setActive($split[1]))
					{
						$this->active = true;
					}
				}
			}
		}

		return $this->active;
	}

	/**
	 * @param $actives string[]
	 */
	function setActives($actives)
	{
		$this->resetActive();

		foreach ($actives as $active)
		{
			$this->setActive($active);
		}
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
	 * @param $str string
	 * @return string[2]
	 */
	private static function splitAtFirstPeriod($str)
	{
		$pos = strpos($str, ".");
		$pos = $pos === false ? $pos = strlen($str) : $pos;

		$first_part = substr($str, 0, $pos);
		$last_part = substr($str, $pos + 1);
		$last_part = $last_part === false ? "" : $last_part;

		return array($first_part, $last_part);
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
