<?php 
require_once(dirname(__FILE__)."/config.php");
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
	
	$nowtme = time();
	if($filename!="") $filename = trim(ereg_replace("[ \r\n\t\*\%\\/\?><\|\":]{1,}","",$filename));
	if($filename==""){
		$y = substr(strftime("%Y",$nowtme),2,2);
		$filename = $cuserLogin->getUserID()."_".$y.strftime("%m%d%H%M%S",$nowtme);
		$fs = explode(".",$uploadfile_name);
		$filename = $filename.".".$fs[count($fs)-1];
	}
  $fullfilename = $cfg_basedir.$activepath."/".$filename;
  if(file_exists($fullfilename)){
  	ShowMsg("本目录已经存在同名的文件，请更改！","-1");
		exit();
  }
  @move_uploaded_file($uploadfile,$fullfilename);
	if($uploadfile_type == 'application/x-shockwave-flash') $mediatype=2;
	else if(eregi('audio|media|video',$uploadfile_type)) $mediatype=3;
	else $mediatype=4;
	$inquery = "
   INSERT INTO #@__uploads(title,url,mediatype,width,height,playtime,filesize,uptime,adminid,memberid) 
   VALUES ('$filename','".$activepath."/".$filename."','$mediatype','0','0','0','{$uploadfile_size}','{$nowtme}','".$cuserLogin->getUserID()."','0');
  ";
  $dsql = new DedeSql(false);
  $dsql->ExecuteNoneQuery($inquery);
  $dsql->Close();
	ShowMsg("成功上传文件！","select_media.php?comeback=".urlencode($filename)."&f=$f&activepath=".urlencode($activepath)."&d=".time());
	exit();
}
?>