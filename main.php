<?php
require_once "common.php";
use Psr\Http\Message\ServerRequestInterface as Request;
$cache = new My\MemcacheLRU("db.cache");

$Router->add("get", "/", function (Request $request) use ($cache) {
	echo "Nothing to see here. Move along sir.";
});




$Router->add("all", "/api/set", function (Request $request) use ($cache) {
	$parse_json = function($data)	{
		try {
			$data = json_decode($data);
			if (empty($data)) return 400;
			foreach ($data as $key => $value)
				if (!is_string($key)) return 400;
			return $data;
		}
		catch (Exception $e) {
			return 500;
		}
	};
	$parse_form_data = function()	{
		if (!isset($_REQUEST['name']) or !isset($_REQUEST['value'])) return 400;

		$name = $_REQUEST['name'];
		$value = $_REQUEST['value'];

		if (is_array($name) and count($name)!=count($value)) return 400;

		if (!is_array($name))
			return [$name => $value];
		else
			return array_combine($name, $value);
	};

	global $_HEADER;
	if (strtolower(@$_HEADER['CONTENT-TYPE']) == "application/json")
		$req = $parse_json($request->getBody());
	else
		$req = $parse_form_data();

	if (is_numeric($req)) return $req;

	foreach ($req as $key=>$val)
		$cache->set($key, $val);
});

$Router->add("all", "/api/(get|del)", function (Request $request) use ($cache) {
	$parse_json = function($data)	{
		try {
			$data = json_decode($data);
			if (!is_array($data)
				or count($data)==0)
				return 400;
			foreach ($data as $key)
				if (!is_string($key)) return 400;
			return $data;
		}
		catch (Exception $e) {
			return 500;
		}
	};
	$parse_form_data = function()	{
		if (!isset($_REQUEST['name'])) return 400;

		$name = $_REQUEST['name'];
		if (!is_array($name))
			$name = [$name];
		return $name;
	};

	global $_HEADER;
	if (strtolower(@$_HEADER['CONTENT-TYPE']) == "application/json")
		$req = $parse_json($request->getBody());
	else
		$req = $parse_form_data();
	if (is_numeric($req)) return $req;

	$uri = $request->getUri()->getPath();
	$type = substr($uri, -3);

	if ($type == "del")
	{
		foreach ($req as $name)
			$cache->unset($name);
	}
	else
	{
		$out = [];
		foreach ($req as $name)
			$out[$name] = $cache->get($name);
		echo json_encode($out, JSON_PRETTY_PRINT);
	}
});


$Router->run('0.0.0.0:8080');



