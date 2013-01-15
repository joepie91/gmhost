<?php

namespace TahoeLafs;

class NotADirectoryException extends \Exception {}
class NotAFileException extends \Exception {}
class UnreachableException extends \Exception {}
class NonexistentException extends \Exception {}

class Filesystem
{
	public function __construct($url)
	{
		$this->url = $url;
		$statusdata = $this->GetJson("/statistics?t=json");
		
		if($statusdata === null)
		{
			throw new UnreachableException("Malformed data received.");
		}
		
		$this->status = $statusdata;
	}
	
	
	public function Get($url)
	{
		return curl_get($this->url . $url);
	}
	
	public function GetJson($url)
	{
		return json_decode($this->Get($url), true);
	}
	
	public function Post($url, $parameters)
	{
		return curl_post($this->url . $url, $parameters);
	}
	
	public function PostJson($url, $parameters)
	{
		return json_decode($this->Post($url, $parameters), true);
	}
	
	public function Put($url, $file)
	{
		return curl_put($this->url . $url, $file);
	}
	
	public function CreateDirectory()
	{
		$uri = $this->Post("/uri?t=mkdir", array());
		
		if(empty($uri))
		{
			throw new UnreachableException("Invalid directory URI received.");
		}
		
		$dir = new Directory($uri, $this);
		return $dir;
	}
}

class Directory
{
	public $files = array();
	public $directories = array();
	private $data = array();
	
	public function __construct($uri, $fs, $data = null)
	{
		$this->fs = $fs;
		$this->uri = $uri;
		
		if($data === null)
		{
			$this->GetDetails();
		}
		else
		{
			$this->data = $data;
		}
		
		if($this->data === null)
		{
			throw new NonexistentException("Directory does not exist.");
		}
		
		$this->ParseDetails();
	}
	
	private function GetDetails()
	{
		$data = $this->fs->GetJson("/uri/{$this->uri}?t=json");
		
		if($data !== null && $data[0] != "dirnode")
		{
			throw new NotADirectoryException("The URI does not represent a directory.");
		}
		
		$this->data = $data[1];
	}
	
	private function ParseDetails()
	{
		if(array_key_exists("rw_uri", $this->data))
		{
			$this->writable = true;
			$this->writecap = $this->data["rw_uri"];
		}
		else
		{
			$this->writable = false;
		}
		
		if(array_key_exists("ro_uri", $this->data))
		{
			$this->readable = true;
			$this->readcap = $this->data["ro_uri"];
		}
		else
		{
			$this->readable = false;
		}
		
		if(array_key_exists("verify_uri", $this->data))
		{
			$this->verifiable = true;
			$this->verifycap = $this->data["verify_uri"];
		}
		else
		{
			$this->verifiable = false;
		}
		
		if(array_key_exists("metadata", $this->data))
		{
			$this->metadata = $this->data["metadata"]["tahoe"];
		}
		
		if(array_key_exists("children", $this->data))
		{
			foreach($this->data["children"] as $childname => $child)
			{
				if(array_key_exists("rw_uri", $child[1]))
				{
					$newuri = $child[1]["rw_uri"];
				}
				else
				{
					$newuri = $child[1]["ro_uri"];
				}
				
				if($child[0] == "dirnode")
				{
					$newchild = new Directory($newuri, $this->fs, $child[1]);
					$this->directories[$childname] = $newchild;
				}
				elseif($child[0] == "filenode")
				{
					$newchild = new File($newuri, $this->fs, $child[1]);
					$this->files[$childname] = $newchild;
				}
				else
				{
					continue;
				}
			}
		}
		
		$this->mutable = $this->data["mutable"];
	}
	
	public function CreateDirectory($name)
	{
		$writecap = $this->fs->Post("/uri/{$this->uri}?t=mkdir&name=" . tahoeencode($name));
		$dir = new Directory($writecap, $this->fs);
		return $dir;
	}
	
	public function UploadFile($name, $path)
	{
		$uri = $this->fs->Put("/uri/" . rtrim($this->uri, "/") . "/" . tahoeencode($name), $path);
		$newfile = new File($uri, $this->fs);
		return $newfile;
	}
	
	public function CheckIfExists($name)
	{
		try
		{
			$f = new File(rtrim($this->uri, "/") . "/" . tahoeencode($name), $this->fs);
			return true;
		}
		catch(NotAFileException $e)
		{
			return true;
		}
		catch(\Exception $e)
		{
			return false;
		}
	}
}

class File
{
	public function __construct($uri, $fs, $data = null)
	{
		$this->fs = $fs;
		$this->uri = $uri;
		
		if($data === null)
		{
			$this->GetDetails();
		}
		else
		{
			$this->data = $data;
		}
		
		if($this->data === null)
		{
			throw new NonexistentException("File does not exist.");
		}
		
		$this->ParseDetails();
	}
	
	private function GetDetails()
	{
		$data = $this->fs->GetJson("/uri/{$this->uri}?t=json");
		
		if($data !== null && $data[0] != "filenode")
		{
			throw new NotAFileException("The URI does not represent a file.");
		}
		
		$this->data = $data[1];
	}
	
	private function ParseDetails()
	{
		if(array_key_exists("rw_uri", $this->data))
		{
			$this->writable = true;
			$this->writecap = $this->data["rw_uri"];
		}
		else
		{
			$this->writable = false;
		}
		
		if(array_key_exists("ro_uri", $this->data))
		{
			$this->readable = true;
			$this->readcap = $this->data["ro_uri"];
		}
		else
		{
			$this->readable = false;
		}
		
		if(array_key_exists("verify_uri", $this->data))
		{
			$this->verifiable = true;
			$this->verifycap = $this->data["verify_uri"];
		}
		else
		{
			$this->verifiable = false;
		}
		
		if(array_key_exists("metadata", $this->data))
		{
			$this->metadata = $this->data["metadata"]["tahoe"];
		}
		
		$this->mutable = $this->data["mutable"];
		$this->size = $this->data["size"];
	}
	
	public function Download()
	{
		$target = $this->fs->url . "/uri/" . $this->uri;
		
		$parts = explode("/", $this->uri);
		$name = array_pop($parts);
		
		if(substr($name, 0, 4) != "URI:")
		{
			$filename = $name;
		}
		else
		{
			$filename = $file;
		}
		
		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename=' . $filename);
		header('Content-Transfer-Encoding: binary');
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		header('Content-Length: ' . $this->size);
		
		readfile($target);
	}
}

function tahoeencode($text)
{
	return str_replace("%2F", "/", rawurlencode($text));
}
