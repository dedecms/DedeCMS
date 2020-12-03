<?php
require_once(dirname(__FILE__)."/config.php");
require_once(dirname(__FILE__)."/../inc_photograph.php");
if(empty($job)) $job = "";
CheckUserSpace($cfg_ml->M_ID);

//检测或创建用户目录
$rootdir = $cfg_user_dir."/".$cfg_ml->M_ID;

if(!is_dir($cfg_basedir.$rootdir)){
	CreateDir($rootdir);
	CloseFtp();
}

if(empty($uploadfile)) $uploadfile="";
	
if(!is_uploaded_file($uploadfile)){
		ShowMsg("你没有选择上传的文件!","-1");
	  exit();
}
	
if($uploadfile_size > $cfg_mb_upload_size*1024){
	  @unlink(is_uploaded_file($uploadfile));
		ShowMsg("你上传的文件超过了{$cfg_mb_upload_size}K，不允许上传！","-1");
		exit();
}

	
if(!CheckAddonType($uploadfile_name)){
	ShowMsg("你所上传的文件类型被禁止，系统只允许上传<br>".$cfg_mb_mediatype." 类型附件！","-1");
	exit();
}
	
$fs = explode(".",$uploadfile_name);
$sname = trim($fs[count($fs)-1]);

if($sname==''){
	ShowMsg("你所上传的文件无法识别，系统禁止上传<br />","-1");
	exit();
}
	
$nowtme = time();
	
	
$filename_name = dd2char($cfg_ml->M_ID."0".strftime("%y%m%d%H%M%S",$nowtme)."0".mt_rand(1000,9999));
	
$filename = $filename_name.".".$sname; //这里用不带目录的文件名作标题
	
$fileurl = $rootdir."/".$filename;

$fullfilename = $cfg_basedir.$fileurl;
  
//严格检查最终的文件名
if(!CheckAddonType($fullfilename) || eregi("\.(php|asp|pl|shtml|jsp|cgi|aspx)",$fullfilename)){
	ShowMsg("你所上传的文件类型被禁止，系统只允许上传<br>".$cfg_mb_mediatype." 类型附件！","-1");
	exit();
}
	
@move_uploaded_file($uploadfile,$fullfilename);
  
//if(empty($resize)) $resize = 0;

$imgwidthValue = 0;
$imgheightValue = 0;
if(in_array($uploadfile_type,$cfg_photo_typenames)){
  $info = "";
  $sizes[0] = 0; $sizes[1] = 0;
	@$sizes = getimagesize($fullfilename,$info);
	$imgwidthValue = $sizes[0];
	$imgheightValue = $sizes[1];
}

$fsize = filesize($fullfilename);

if(eregi('image',$uploadfile_type)) $ftype = 1;
else if(eregi('audio|video',$uploadfile_type))$ftype = 2;
else if($uploadfile_type=='application/x-shockwave-flash'||$sname=='swf') $ftype = 3;
else $ftype = 4;
  
if(empty($title)) $title = $filename;

$inquery = "
   INSERT INTO #@__uploads(title,url,mediatype,width,height,playtime,filesize,uptime,adminid,memberid) 
   VALUES ('$title','$fileurl','$ftype','$imgwidthValue','$imgheightValue','0','$fsize','$nowtme','0','{$cfg_ml->M_ID}');
";
  

$dsql = new DedeSql(false);
$dsql->ExecuteNoneQuery($inquery);
$dsql->Close();
  
if(empty($ENV_GOBACK_URL)) $ENV_GOBACK_URL = "all_medias.php";

@unlink($uploadfile);
ShowMsg("成功上传附件！",$ENV_GOBACK_URL);
exit();

?>