<?php

class Target
{
	private $name;
	private $children;

	function __construct($name)
	{
		$this->name = $name;
		$this->children = array();
	}

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