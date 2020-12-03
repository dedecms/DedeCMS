<?php 
require_once(dirname(__FILE__)."/config.php");
if(empty($dopost)) $dopost = "";
isset($_COOKIE['ENV_GOBACK_URL']) ? $backurl=$_COOKIE['ENV_GOBACK_URL'] : $backurl="javascript:history.go(-1);";

/*---------------------------
function __del_file() //删除附件
-----------------------------*/
if($dopost=='del'){
	CheckPurview('sys_DelUpload');
	if(empty($ids)) $ids="";
	$dsql = new DedeSql(false);
	if($ids==""){
    $myrow = $dsql->GetOne("Select url From #@__uploads where aid='".$aid."'");
    $truefile = $cfg_basedir.$myrow['url'];
    $rs = 0;
		if(!file_exists($truefile)||$myrow['url']=="") $rs = 1;
		else $rs = @unlink($truefile);
    if($rs==1){
       $msg = "成功删除一个附件！";
       $dsql->ExecuteNoneQuery("Delete From #@__uploads where aid='".$aid."'");
    }
    $dsql->Close();
    ShowMsg($msg,$backurl);
    exit();
	}else{
		$ids = explode(',',$ids);
		$idquery = "";
		foreach($ids as $aid){
			if($idquery=="") $idquery .= " where aid='$aid' ";
			else $idquery .= " Or aid='$aid' ";
		}
		$dsql->SetQuery("Select aid,url From #@__uploads $idquery ");
		$dsql->Execute();
		while($myrow=$dsql->GetArray()){
			$truefile = $cfg_basedir.$myrow['url'];
			$rs = 0;
			if(!file_exists($truefile)||$myrow['url']=="") $rs = 1;
			else $rs = @unlink($truefile);
			if($rs==1){
				$dsql->ExecuteNoneQuery("Delete From #@__uploads where aid='".$myrow['aid']."'"); 
			}
		}
		$dsql->Close();
		ShowMsg('成功删除选定的文件！',$backurl);
		exit();
	}
}
/*--------------------------------
function __save_edit() //保存更改
-----------------------------------*/
else if($dopost=='save'){
	if($aid=="") exit();
	//检查是否有修改权限
	$dsql = new DedeSql(false);
  $myrow = $dsql->GetOne("Select * From #@__uploads where aid='".$aid."'");
  $dsql->Close();
	if($myrow['adminid']!=$cuserLogin->getUserID()){ CheckPurview('sys_Upload'); }
	//检测文件类型
	$addquery = "";
	if(is_uploaded_file($upfile)){
		 if($mediatype==1){
		 	  $sparr = Array("image/pjpeg","image/jpeg","image/gif","image/png","image/x-png","image/wbmp");
		 	  if(!in_array($upfile_type,$sparr)){
		 	  	ShowMsg("你上传的不是图片类型的文件！","javascript:history.go(-1);");
		 	  	exit();
		 	  }
		 }
     else if($mediatype==2){
     	  $sparr = Array("application/x-shockwave-flash");
     	  if(!in_array($upfile_type,$sparr)){
		 	  	ShowMsg("你上传的不是Flash类型的文件！","javascript:history.go(-1);");
		 	  	exit();
		 	  }
     }else if($mediatype==3){
     	  if(!eregi('audio|media|video',$upfile_type)){
     	  	ShowMsg("你上传的为不正确类型的影音文件！","javascript:history.go(-1);");
		 	  	exit();
     	  }
     	  if(!eregi("\.".$cfg_mediatype,$upfile_name)){
     	  	ShowMsg("你上传的影音文件扩展名无法被识别，请更改系统配置的参数！","javascript:history.go(-1);");
		 	  	exit();
     	  }
     }else{
     	  if(!eregi("\.".$cfg_softtype,$upfile_name)){
     	  	ShowMsg("你上传的附件扩展名无法被识别，请更改系统配置的参数！","javascript:history.go(-1);");
		 	  	exit();
     	  } 
     }
     //保存文件
     $nowtime = time();
     $oldfile = $myrow['url'];
     $oldfiles = explode('/',$oldfile);
     $fullfilename = $cfg_basedir.$oldfile;
     $oldfile_path = ereg_replace($oldfiles[count($oldfiles)-1]."$","",$oldfile);
		 if(!is_dir($cfg_basedir.$oldfile_path)){
		 	  MkdirAll($cfg_basedir.$oldfile_path,$GLOBALS['cfg_dir_purview']);
		 	  CloseFtp();
		 }
		 @move_uploaded_file($upfile,$fullfilename);
		 if($mediatype==1){
		 	  require_once(dirname(__FILE__)."/../include/inc_photograph.php");
		 	  if(in_array($upfile_type,$cfg_photo_typenames)) WaterImg($fullfilename,'up');
		 }
		 $filesize = $upfile_size;
		 $imgw = 0;
		 $imgh = 0;
		 if($mediatype==1){
		 	 $info = "";
       $sizes[0] = 0; $sizes[1] = 0;
	     @$sizes = getimagesize($fullfilename,$info);
	     $imgw = $sizes[0];
	     $imgh = $sizes[1];
		 }
		 if($imgw>0) $addquery = ",width='$imgw',height='$imgh',filesize='$filesize' ";
		 else $addquery = ",filesize='$filesize' ";
	}
	else{ $fileurl = $filename; }
  //写入数据库
  $query = " update #@__uploads set title='$title',mediatype='$mediatype',playtime='$playtime'";
  $query .= "$addquery where aid='$aid' ";
  $dsql = new DedeSql(false);
  $dsql->ExecuteNoneQuery($query);
  $dsql->Close();
  ShowMsg('成功更改一则附件数据！','media_edit.php?aid='.$aid);
  exit();
}
else
{
  //读取档案信息
  //--------------------------------
  CheckPurview('sys_Upload,sys_MyUpload');
  $dsql = new DedeSql(false);
  $myrow = $dsql->GetOne("Select * From #@__uploads where aid='".$aid."'");
  $dsql->Close();
  if(!is_array($myrow)){
	  ShowMsg('错误，找不到此编号的档案！','javascript:;');
	  exit();
  }
}

require_once(dirname(__FILE__)."/templets/media_edit.htm");

ClearAllLink();
?>