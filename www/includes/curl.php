<?php

function curl_get($url, $headers = array())
{
	$curl = curl_init();
	
	curl_setopt_array($curl, array(
		CURLOPT_RETURNTRANSFER => 1,
		CURLOPT_URL => $url,
		CURLOPT_HTTPHEADER => $headers
	));
	
	$result = curl_exec($curl);
	
	if($result === false)
	{
		throw new Exception("Request failed: " . curl_error($curl));
	}
	else
	{
		return $result;
	}
}

function curl_post($url, $parameters, $headers = array())
{
	$curl = curl_init();
	
	curl_setopt_array($curl, array(
		CURLOPT_RETURNTRANSFER => 1,
		CURLOPT_URL => $url,
		CURLOPT_POST => 1,
		CURLOPT_POSTFIELDS => $parameters,
		CURLOPT_HTTPHEADER => $headers
	));
	
	$result = curl_exec($curl);
	
	if($result === false)
	{
		throw new Exception("Request failed: " . curl_error($curl));
	}
	else
	{
		return $result;
	}
}

function curl_put($url, $file, $headers = array())
{
	$handle = fopen($file, "r");
	
	$curl = curl_init();
	
	curl_setopt_array($curl, array(
		CURLOPT_RETURNTRANSFER => 1,
		CURLOPT_URL => $url,
		CURLOPT_PUT => 1,
		CURLOPT_INFILE => $handle,
		CURLOPT_INFILESIZE => filesize($file),
		CURLOPT_HTTPHEADER => $headers
	));
	
	$result = curl_exec($curl);
	
	if($result === false)
	{
		throw new Exception("Request failed: " . curl_error($curl));
	}
	else
	{
		return $result;
	}
}
