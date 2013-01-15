<?php
if(empty($_GMHOST)) { die("Unauthorized."); }

if(empty($_POST['submit']) || empty($_POST['currentdir']))
{
	die();
}

if(empty($_POST['name']))
{
	die("Error: You did not enter a name.");
}

try
{
	$dir = new TahoeLafs\Directory($cphp_config->public_dir . TahoeLafs\tahoeencode($_POST['currentdir']), $fs);
	$newdir = $dir->CreateDirectory($_POST['name']);
	
	header("Location: {$_POST['currentdir']}" . urlencode($_POST['name']));
	die();
}
catch(Exception $e)
{
	die("Error: You are trying to create a directory in a directory that does not exist yet.");
}
