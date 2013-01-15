<?php

$_GMHOST = true;
$_CPHP = true;
$_CPHP_CONFIG = "../config.json";
require("cphp/base.php");
require("includes/curl.php");
require("includes/tahoe.php");

try
{
	$fs = new TahoeLafs\Filesystem("http://localhost:3456");
}
catch (Exception $e)
{
	die("Could not reach server.");
}

$router = new CPHPRouter();

$router->routes = array(
	0 => array(
		"^/upload$"	=> "upload.php",
		"^/newdir$"	=> "newdir.php",
		"^/.*"		=> "list.php"
	)
);

$router->RouteRequest();
