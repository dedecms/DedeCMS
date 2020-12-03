<?php

function GetCtTrueName($cid)
{
	global $cfg_cmspath,$cfg_basedir;
	$ipath = $cfg_cmspath."/data/textdata";
	$tpath = ceil($cid/5000);
	$bookfile1 = $cfg_basedir."$ipath/$tpath/bk{$cid}.php";
	$bookfile2 = $cfg_basedir."$ipath/$tpath/bk{$cid}.inc";
	if(file_exists($bookfile1)) {
		rename($bookfile1, $bookfile2);
	}
	return $bookfile2;
}

function GetBookText($cid)
{
	global $cfg_cmspath,$cfg_basedir;
	$bookfile = GetCtTrueName($cid);
	if(!file_exists($bookfile))
	{
		return '';
	}
	else
	{
		$alldata = '';
		$fp = fopen($bookfile,'r');
		$line = fgets($fp, 256);
		$alldata = '';
		while(!feof($fp)) {
			$alldata .= fread($fp,1024);
		}
		fclose($fp);
		return trim(substr($alldata,0,strlen($alldata)-2));
	}
}

function WriteBookText($cid,$body)
{
	$body = stripslashes($body);
	global $cfg_cmspath,$cfg_basedir;
	$ipath = $cfg_cmspath."/data/textdata";
	$tpath = ceil($cid/5000);
	if(!is_dir($cfg_basedir.$ipath)) MkdirAll($cfg_basedir.$ipath,$GLOBALS['cfg_dir_purview']);
	if(!is_dir($cfg_basedir.$ipath.'/'.$tpath)) MkdirAll($cfg_basedir.$ipath.'/'.$tpath,$GLOBALS['cfg_dir_purview']);
	$bookfile = $cfg_basedir.$ipath."/{$tpath}/bk{$cid}.inc";
	$body = "<"."?php error_reporting(0); exit();\r\n".$body."\r\n?".">";
	@$fp = fopen($bookfile,'w');
	@flock($fp);
	@fwrite($fp,$body);
	@fclose($fp);
}
?>