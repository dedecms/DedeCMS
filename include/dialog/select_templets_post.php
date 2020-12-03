<?php 
require_once(dirname(__FILE__)."/config.php");
$cfg_txttype = "htm|html|tpl|txt|dtp";
if(empty($job)) $job = "";
if($job=="newdir")
{
	$dirname = trim(ereg_replace("[ \r\n\t\.\*\%\\/\?><\|\":]{1,}","",$dirname));
	if($dirname==""){
		ShowMsg("目录名非法！","-1");
		exit();
	}
	MkdirAll($cfg_basedir.$activepath."/".$dirname,$GLOBALS['cfg_dir_purview']);
	CloseFtp();
	ShowMsg("成功创建一个目录！","select_templets.php?f=$f&activepath=".urlencode($activepath."/".$dirname));
	exit();
}
if($job=="upload")
{
	if(empty($uploadfile)) $uploadfile = "";
	if(!is_uploaded_file($uploadfile)){
		 ShowMsg("你没有选择上传的文件!","-1");
	   exit();
	}
	if(!ereg("^text",$uploadfile_type)){
		ShowMsg("你上传的不是文本类型附件!","-1");
		exit();
	}
	if(!eregi($cfg_txttype,$uploadfile_name))
	{
		ShowMsg("你所上传的模板文件类型不能被识别，请使用htm、html、tpl、txt、dtp扩展名！","-1");
		exit();
	}
	if($filename!="") $filename = trim(ereg_replace("[ \r\n\t\*\%\\/\?><\|\":]{1,}","",$filename));
	if($filename==""){
		$y = substr(strftime("%Y",time()),2,2);
		$filename = $cuserLogin->getUserID()."_".$y.strftime("%m%d%H%M%S",time());
		$fs = explode(".",$uploadfile_name);
		$filename = $filename.".".$fs[count($fs)-1];
	}
  $fullfilename = $cfg_basedir.$activepath."/".$filename;
  if(file_exists($fullfilename))
  {
  	ShowMsg("本目录已经存在同名的文件，请更改！","-1");
		exit();
  }
  @move_uploaded_file($uploadfile,$fullfilename);
	@unlink($uploadfile);
	ShowMsg("成功上传文件！","select_templets.php?comeback=".urlencode($filename)."&f=$f&activepath=".urlencode($activepath)."&d=".time());
	exit();
}
?>