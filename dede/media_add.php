<?php 
require_once(dirname(__FILE__)."/config.php");
if(empty($dopost)) $dopost = "";

if($dopost=="upload") //上传
{
require_once(dirname(__FILE__)."/../include/inc_photograph.php");
$sparr_image = Array("image/pjpeg","image/jpeg","image/gif","image/png","image/x-png","image/wbmp");
$sparr_flash = Array("application/x-shockwave-flash");
$okdd = 0;
$uptime = time();
$adminid = $cuserLogin->getUserID();
$dsql = new DedeSql(false);
for($i=0;$i<=40;$i++)
{
  if(isset(${"upfile".$i}) && is_uploaded_file(${"upfile".$i}))
  {
		$filesize = ${"upfile".$i."_size"};
		$upfile_type = ${"upfile".$i."_type"};
		$upfile_name = ${"upfile".$i."_name"};
		$dpath = strftime("%y%m%d",$uptime);
		if(in_array($upfile_type,$sparr_image)){
			$mediatype=1;
			$savePath = $cfg_image_dir."/".$dpath;
		}
		else if(in_array($upfile_type,$sparr_false)){
			$mediatype=2;
			$savePath = $cfg_other_medias."/".$dpath;
		}
		else if(eregi('audio|media|video',$upfile_type)
	  && eregi("\.".$cfg_softtype."$",$upfile_name)){
		  $mediatype=3;
		  $savePath = $cfg_other_medias."/".$dpath;
		}
		else if(eregi("\.".$cfg_softtype."$",$upfile_name)){
			$mediatype=4;
			$savePath = $cfg_soft_dir."/".$dpath;
		}
		else continue;
		$filename = "{$adminid}_".strftime("%H%M%S",$uptime).mt_rand(100,999).$i;
		$fs = explode(".",${"upfile".$i."_name"});
		$filename = $filename.".".$fs[count($fs)-1];
		$filename = $savePath."/".$filename;
		if(!is_dir($cfg_basedir.$savePath)){
			MkdirAll($cfg_basedir.$savePath,$GLOBALS['cfg_dir_purview']);
			CloseFtp();
		}
		$fullfilename = $cfg_basedir.$filename;
    if($mediatype==1){
       @move_uploaded_file(${"upfile".$i},$fullfilename);
       $info = "";
    	 $data = getImagesize($fullfilename,$info);
    	 $width = $data[0];
    	 $height = $data[1];
		 	if(in_array($upfile_type,$cfg_photo_typenames)) WaterImg($fullfilename,'up');
    }else{
       @move_uploaded_file(${"upfile".$i},$fullfilename);
    }
    if($i>1){ $ntitle = $title."_".$i; }
    else $ntitle = $title;
    $inquery = "
       INSERT INTO #@__uploads(title,url,mediatype,width,height,playtime,filesize,uptime,adminid,memberid) 
       VALUES ('$ntitle','$filename','$mediatype','$width','$height','$playtime','$filesize','$uptime','$adminid','0');
    ";
    $okdd++;
    $dsql->SetQuery($inquery);
    $dsql->ExecuteNoneQuery();
  }
}
$dsql->Close();
ShowMsg("成功上传 {$okdd} 个文件！","media_main.php");
exit();
}

require_once(dirname(__FILE__)."/templets/media_add.htm");

ClearAllLink();
?>