<?php
//--------------------------------
//获得GD的版本
//--------------------------------
function gdversion()
{ 
  static $gd_version_number = null; 
  if ($gd_version_number === null)
  { 
    ob_start(); 
    phpinfo(8); 
    $module_info = ob_get_contents(); 
    ob_end_clean(); 
    if(preg_match("/\bgd\s+version\b[^\d\n\r]+?([\d\.]+)/i", $module_info,$matches))
    {   $gdversion_h = $matches[1];  }
    else
    {  $gdversion_h = 0; }
  } 
  return $gdversion_h; 
}

//----------------------
//返回一个严重错误的警告，并退回上一页
//---------------------
function GetBackAlert($msg,$isstop=0)
{
	$msg = str_replace('"','`',$msg);
  if($isstop==1) $msg = "<script>\r\n<!--\r\n alert(\"{$msg}\");\r\n-->\r\n</script>\r\n";
  else $msg = "<script>\r\n<!--\r\n alert(\"{$msg}\");history.go(-1);\r\n-->\r\n</script>\r\n";
  $msg = "<meta http-equiv=content-type content='text/html; charset=utf-8'>\r\n".$msg;
  return $msg;
}

//测试某目录的写入权限
function TestWrite($d){
	$tfile = '_dedet.txt';
	$fp = @fopen($d.'/'.$tfile,'w');
	if(!$fp) return false;
	else{
		fclose($fp);
		$rs = @unlink($d.'/'.$tfile);
		if($rs) return true;
		else return false;
	}
}
?>