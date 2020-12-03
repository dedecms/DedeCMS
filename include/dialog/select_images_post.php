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
	ShowMsg("成功创建一个目录！","select_images.php?imgstick=$imgstick&f=$f&activepath=".urlencode($activepath."/".$dirname));
	exit();
}
if($job=="upload")
{
	if(empty($imgfile)) $imgfile="";
	if(!is_uploaded_file($imgfile)){
		 ShowMsg("你没有选择上传的文件!","-1");
	   exit();
	}
	if(ereg("^text",$imgfile_type)){
		ShowMsg("不允许文本类型附件!","-1");
		exit();
	}
	$sparr = Array("image/pjpeg","image/jpeg","image/gif","image/png");
  $imgfile_type = strtolower(trim($imgfile_type));
  if(!in_array($imgfile_type,$sparr)){
		ShowMsg("上传的图片格式错误，请使用JPEG、GIF、PNG格式的其中一种！","-1");
		exit();
	}
	$y = substr(strftime("%Y",time()),2,2);
	$filename = $cuserLogin->getUserID()."_".$y.strftime("%m%d%H%M%S",time());
	$fs = explode(".",$imgfile_name);
	$filename = $filename.".".$fs[count($fs)-1];
  $fullfilename = $cfg_basedir.$activepath."/".$filename;
  if(file_exists($fullfilename)){
  	ShowMsg("本目录已经存在同名的文件，请更改！","-1");
		exit();
  }
  @move_uploaded_file($imgfile,$fullfilename);
  
  if(empty($resize)) $resize = 0;
  if($resize==1){
  	require_once(dirname(__FILE__)."/../inc_photograph.php");
  	ImageResize($fullfilename,$iwidth,$iheight);
  }
  
	@unlink($imgfile);
	ShowMsg("成功上传一幅图片！","select_images.php?imgstick=$imgstick&comeback=".urlencode($filename)."&f=$f&activepath=".urlencode($activepath)."&d=".time());
	exit();
}
?>