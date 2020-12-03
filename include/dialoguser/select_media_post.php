<?
require_once(dirname(__FILE__)."/config.php");
if(empty($job)) $job = "";


if($cfg_mb_upload=='否'){
	$dsql->Close();
	ShowMsg("系统不允许会员上传非图片附件!","-1");
	exit();
}
//检测用户文件存放路径是否合法
$activepath = str_replace("\\","/",$activepath);
$activepath = ereg_replace("^/{1,}","/",$activepath);
$rootdir = $cfg_user_dir."/".$cfg_ml->M_ID;

if(ereg("\.",$activepath)){
	echo "你访问的目录不合法！";
	exit();
}

if(!eregi($rootdir,$activepath)){
	echo "你访问的目录不合法！";
	exit();
}

if(strlen($activepath) < strlen($rootdir)){
	$activepath = $rootdir;
}

if($job=="newdir")
{
	$dirname = trim(ereg_replace("[\s\.\*\%\\/\?><\|\":]{1,}","",$dirname));
	if($dirname==""){
		ShowMsg("目录名非法！","-1");
		exit();
	}
	MkdirAll($cfg_basedir.$activepath."/".$dirname,777);
	CloseFtp();
	ShowMsg("成功创建一个目录！","select_media.php?f=$f&activepath=".urlencode($activepath."/".$dirname));
	exit();
}
if($job=="upload")
{
	CheckUserSpace($cfg_ml->M_ID);
	if(empty($uploadfile)) $uploadfile = "";
	if(!is_uploaded_file($uploadfile)){
		 ShowMsg("你没有选择上传的文件!","-1");
	   exit();
	}
	if(eregi("^text",trim($uploadfile_type))){
		ShowMsg("不允许文本类型附件!","-1");
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
	$nowtme = mytime();
	
	//不再支持文件改名
  $y = substr(strftime("%Y",$nowtme),2,2);
	$filename = $cfg_ml->M_ID."_".$y.strftime("%m%d%H%M%S",$nowtme);
	$fs = explode(".",$uploadfile_name);
	$filename = $filename.".".$fs[count($fs)-1];
	
  $fullfilename = $cfg_basedir.$activepath."/".$filename;
  if(file_exists($fullfilename)){
  	ShowMsg("本目录已经存在同名的文件，请更改！","-1");
		exit();
  }
  
  //严格检查最终的文件名
  if(!CheckAddonType($fullfilename) || eregi("\.(php|asp|pl|shtml|jsp|cgi|aspx)",$fullfilename)){
		ShowMsg("你所上传的文件类型被禁止，系统只允许上传<br>".$cfg_mb_mediatype." 类型附件！","-1");
		exit();
	}
  
  @move_uploaded_file($uploadfile,$fullfilename);
  
	if($uploadfile_type == 'application/x-shockwave-flash') $mediatype=2;
	else if(eregi('audio|media|video',$uploadfile_type)) $mediatype=3;
	else $mediatype=4;
	$inquery = "
   INSERT INTO #@__uploads(title,url,mediatype,width,height,playtime,filesize,uptime,adminid,memberid) 
   VALUES ('$filename','".$activepath."/".$filename."','$mediatype','0','0','0','{$uploadfile_size}','{$nowtme}','0','".$cfg_ml->M_ID."');
  ";
  $dsql = new DedeSql(false);
  $dsql->ExecuteNoneQuery($inquery);
  $dsql->Close();
	ShowMsg("成功上传文件！","select_media.php?comeback=".urlencode($filename)."&f=$f&activepath=".urlencode($activepath)."&d=".time());
	exit();
}
?>