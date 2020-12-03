<?
require_once(dirname(__FILE__)."/config.php");
if(empty($job)) $job = "";
if($job=="newdir")
{
	$dirname = trim(ereg_replace("[ \r\n\t\.\*\%\\/\?><\|\":]{1,}","",$dirname));
	if($dirname==""){
		ShowMsg("目录名非法！","-1");
		exit();
	}
	@mkdir($cfg_basedir.$activepath."/".$dirname,$cfg_dir_purview);
	ShowMsg("成功创建一个目录！","select_media.php?f=$f&activepath=".urlencode($activepath."/".$dirname));
	exit();
}
if($job=="upload")
{
	if(empty($uploadfile)) $uploadfile = "";
	if(!is_uploaded_file($uploadfile)){
		 ShowMsg("你没有选择上传的文件!","-1");
	   exit();
	}
	if(ereg("^text",$uploadfile_type)){
		ShowMsg("不允许文本类型附件!","-1");
		exit();
	}
	if(!eregi($cfg_mediatype,$uploadfile_name))
	{
		ShowMsg("你所上传的媒体类型不能被识别，请更改config_base.php里的配置！","-1");
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
	ShowMsg("成功上传文件！","select_media.php?comeback=".urlencode($filename)."&f=$f&activepath=".urlencode($activepath)."&d=".time());
	exit();
}
?>