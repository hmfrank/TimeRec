<?php

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
			echo "<span " . ($this->active ? "class=\"active\"" : "") . ">" . $this->name . "</span>\n";
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

	/**
	 * @param $filename string
	 * @return Target
	 */
	static function fromConfFile($filename)
	{
		$string = file_get_contents($filename);
		if ($string === false)
		{
			return new Target("");
		}

		$json = json_decode($string, true);
		if ($json === null)
		{
			return new Target("");
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