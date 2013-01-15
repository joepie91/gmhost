<?php
if(empty($_GMHOST)) { die("Unauthorized."); }

if(empty($_POST['submit']) || empty($_POST['currentdir']))
{
	die();
}

if(empty($_FILES))
{
	die("Error: You did not select a file.");
}

try
{
	$dir = new TahoeLafs\Directory($cphp_config->public_dir . TahoeLafs\tahoeencode(rtrim($_POST['currentdir'], "/")), $fs);
	
	if($_FILES["file"]["error"] == UPLOAD_ERR_OK)
	{
		if($dir->CheckIfExists($_FILES["file"]["name"]) === false)
		{
			$dir->UploadFile($_FILES["file"]["name"], $_FILES["file"]["tmp_name"]);
		}
		else
		{
			die("A file or directory with that name already exists.");
		}
		header("Location: {$_POST['currentdir']}");
		die();
	}
	else
	{
		die("Error {$_FILES['file']['error']} occurred while uploading.");
	}
	
}
catch(TahoeLafs\NonexistentException $e)
{
	die("Error: You are trying to upload a file in a directory that does not exist yet.");
}
