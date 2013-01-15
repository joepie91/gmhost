<?php
if(empty($_GMHOST)) { die("Unauthorized."); }

if(!empty($_SERVER['REQUEST_URI']))
{
	$requestpath = urldecode(trim($_SERVER['REQUEST_URI']));
}
else
{
	$requestpath = "/";
}

if($requestpath == "/")
{
	$parent_url = "#";
	$chunks = array("");
}
else
{
	$path = rtrim($requestpath, "/");
	$chunks = explode("/", $path);
	array_pop($chunks);
	$parent_url = implode("/", $chunks);
	
	if(substr($parent_url, 0, 1) !== "/")
	{
		$parent_url = "/" . $parent_url;
	}
	
	$requestpath = $path . "/";
}

try
{
	try
	{
		$dir = new TahoeLafs\Directory($cphp_config->public_dir . TahoeLafs\tahoeencode(rtrim($requestpath, "/")), $fs);

		$directories = array();
		$files = array();

		asort($dir->directories);
		asort($dir->files);

		foreach($dir->directories as $directory => $directory_data)
		{
			$directories[] = array(
				"name"		=> htmlspecialchars($directory),
				"url"		=> rawurlencode($directory) . "/"
			);
		}

		foreach($dir->files as $file => $file_data)
		{
			$files[] = array(
				"name"		=> htmlspecialchars($file),
				"size"		=> $file_data->size,
				"url"		=> rawurlencode($file)
			);
		}

		echo(NewTemplater::Render("index", $locale->strings, array(
			"directories"	=> $directories,
			"files"		=> $files,
			"parent-url"	=> htmlspecialchars($parent_url),
			"current-dir"	=> htmlspecialchars($requestpath)
		)));
	}
	catch(TahoeLafs\NotADirectoryException $e)
	{
		$file = new TahoeLafs\File($cphp_config->public_dir . TahoeLafs\tahoeencode(rtrim($requestpath, "/")), $fs);
		$file->Download();
	}
}
catch(TahoeLafs\NonexistentException $e)
{
	echo("The specified directory or file does not exist (anymore).");
}
