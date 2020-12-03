<?
require_once(dirname(__FILE__)."/config.php");
require_once(dirname(__FILE__)."/../inc_photograph.php");
if(empty($job)) $job = "";

//检测用户文件存放路径是否合法
$activepath = str_replace("\\","/",$activepath);
$activepath = str_replace("..","",$activepath);
$activepath = ereg_replace("^/{1,}","/",$activepath);
$rootdir = $cfg_user_dir."/".$cfg_ml->M_ID;
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
	ShowMsg("成功创建一个目录！","select_images.php?imgstick=$imgstick&v=$v&f=$f&activepath=".urlencode($activepath."/".$dirname));
	exit();
}
if($job=="upload")
{
	CheckUserSpace($cfg_ml->M_ID);
	if(empty($imgfile)) $imgfile="";
	if(!is_uploaded_file($imgfile)){
		 ShowMsg("你没有选择上传的文件!","-1");
	   exit();
	}
	if(eregi("^text",trim($uploadfile_type))){
		ShowMsg("不允许文本类型附件!","-1");
		exit();
	}
	if($imgfile_size > $cfg_mb_upload_size*1024){
	   @unlink(is_uploaded_file($imgfile));
		 ShowMsg("你上传的文件超过了{$cfg_mb_upload_size}K，不允许上传！","-1");
		 exit();
	}
	if(!eregi("\.(jpg|gif|png|bmp)$",$imgfile_name)){
		ShowMsg("你所上传的文件类型被禁止！".$imgfile_name,"-1");
		exit();
	}
	$nowtme = mytime();
	$sparr = Array("image/pjpeg","image/jpeg","image/gif","image/png","image/x-png","image/wbmp");
  $imgfile_type = strtolower(trim($imgfile_type));
  if(!in_array($imgfile_type,$sparr)){
		ShowMsg("上传的图片格式错误，请使用JPEG、GIF、PNG、WBMP格式的其中一种！","-1");
		exit();
	}
	$mdir = strftime("%y%m%d",$nowtme);
	if(!is_dir($cfg_basedir.$activepath."/$mdir")){
		 MkdirAll($cfg_basedir.$activepath."/$mdir",777);
		 CloseFtp();
	}
	$filename_name = $cfg_ml->M_ID."_".dd2char(strftime("%H%M%S",$nowtme).mt_rand(100,999));
	$filename = $mdir."/".$filename_name;
	$fs = explode(".",$imgfile_name);
	$filename = $filename.".".$fs[count($fs)-1];
	$filename_name = $filename_name.".".$fs[count($fs)-1];
  $fullfilename = $cfg_basedir.$activepath."/".$filename;
  if(file_exists($fullfilename)){
  	ShowMsg("本目录已经存在同名的文件，请更改！","-1");
		exit();
  }
  
  @move_uploaded_file($imgfile,$fullfilename);
  
  if(empty($resize)) $resize = 0;
  
  if($resize==1){
  	if(in_array($imgfile_type,$cfg_photo_typenames)) ImageResize($fullfilename,$iwidth,$iheight);
  }
  else{
  	if(in_array($imgfile_type,$cfg_photo_typenames)) WaterImg($fullfilename,'up');
  }
  
  $info = "";
  $sizes[0] = 0; $sizes[1] = 0;
	@$sizes = getimagesize($fullfilename,$info);
	$imgwidthValue = $sizes[0];
	$imgheightValue = $sizes[1];
	$imgsize = filesize($fullfilename);
  
	$inquery = "
   INSERT INTO #@__uploads(title,url,mediatype,width,height,playtime,filesize,uptime,adminid,memberid) 
   VALUES ('$filename','".$activepath."/".$filename."','1','$imgwidthValue','$imgheightValue','0','{$imgsize}','{$nowtme}','0','".$cfg_ml->M_ID."');
  ";
  
  $dsql = new DedeSql(false);
  $dsql->ExecuteNoneQuery($inquery);
  $dsql->Close();
  
	@unlink($imgfile);
	ShowMsg("成功上传一幅图片！","select_images.php?imgstick=$imgstick&comeback=".urlencode($filename_name)."&v=$v&f=$f&activepath=".urlencode($activepath)."/$mdir&d=".time());
	exit();
}
?>