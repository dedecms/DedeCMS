<?php

function GetBookText($cid)
{
	global $cfg_cmspath,$cfg_basedir;
	$ipath = $cfg_cmspath."/data/textdata";
	$tpath = ceil($cid/5000);
	$bookfile = $cfg_basedir."$ipath/$tpath/bk{$cid}.php";
	if(!file_exists($bookfile)) return '';
	else{
		$alldata = '';
		$fp = fopen($bookfile,'r');
		$line = fgets($fp,64);
		$alldata = '';
		while(!feof($fp)){
			$alldata .= fread($fp,1024);
		}
		fclose($fp);
		return trim(substr($alldata,0,strlen($alldata)-2));
	}
}

function WriteBookText($cid,$body)
{
	global $cfg_cmspath,$cfg_basedir;
	$ipath = $cfg_cmspath."/data/textdata";
	$tpath = ceil($cid/5000);
	if(!is_dir($cfg_basedir.$ipath)) MkdirAll($cfg_basedir.$ipath,$GLOBALS['cfg_dir_purview']);
	if(!is_dir($cfg_basedir.$ipath.'/'.$tpath)) MkdirAll($cfg_basedir.$ipath.'/'.$tpath,$GLOBALS['cfg_dir_purview']);
	$bookfile = $cfg_basedir.$ipath."/{$tpath}/bk{$cid}.php";
	$body = "<"."?php exit();\r\n".$body."\r\n?".">";
	@$fp = fopen($bookfile,'w');
  @flock($fp);
  @fwrite($fp,$body);
  @fclose($fp);
}

?>