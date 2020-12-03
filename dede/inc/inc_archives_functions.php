<?
if(!isset($registerGlobals)){ require_once(dirname(__FILE__)."/../../include/config_base.php"); }
require_once(dirname(__FILE__)."/../../include/pub_httpdown.php");
require_once(dirname(__FILE__)."/../../include/inc_archives_view.php");
require_once(dirname(__FILE__)."/../../include/inc_photograph.php");
//---------------------------
//获得文章body里的外部资源
//---------------------------
function GetCurContent($body)
{
	global $cfg_multi_site,$cfg_basehost;
	$cfg_uploaddir = $GLOBALS['cfg_image_dir'];
	$cfg_basedir = $GLOBALS['cfg_basedir'];
	$htd = new DedeHttpDown();
	
	$basehost = "http://".$_SERVER["HTTP_HOST"];
  if($cfg_multi_site == '否'){
    $body = str_replace(strtolower($basehost),"",$body);
    $body = str_replace(strtoupper($basehost),"",$body);
  }else{
  	if($cfg_basehost!=$basehost){
  		$body = str_replace(strtolower($basehost),$cfg_basehost,$body);
  		$body = str_replace(strtoupper($basehost),$cfg_basehost,$body);
  	}
  }
	$img_array = array();
	preg_match_all("/(src|SRC)=[\"|'| ]{0,}(http:\/\/(.*)\.(gif|jpg|jpeg|bmp|png))/isU",$body,$img_array);
	$img_array = array_unique($img_array[2]);
	
	$imgUrl = $cfg_uploaddir."/".strftime("%y%m%d",mytime());
	$imgPath = $cfg_basedir.$imgUrl;
	if(!is_dir($imgPath."/")){
		MkdirAll($imgPath,777);
		CloseFtp();
	}
	$milliSecond = strftime("%H%M%S",mytime());
	
	foreach($img_array as $key=>$value)
	{
		if(eregi($basehost,$value)) continue;
		if($cfg_basehost!=$basehost && eregi($cfg_basehost,$value)) continue;
		if(!eregi("^http://",$value)) continue;
		//随机命名文件
		$htd->OpenUrl($value);
		$itype = $htd->GetHead("content-type");
		if($itype=="image/gif") $itype = ".gif";
		else if($itype=="image/png") $itype = ".png";
		else $itype = ".jpg";
		$value = trim($value);
		$rndFileName = $imgPath."/".$milliSecond.$key.$itype;
		$fileurl = $imgUrl."/".$milliSecond.$key.$itype;
		//下载并保存文件
		$rs = $htd->SaveToBin($rndFileName);
		if($rs){
			$body = str_replace($value,$fileurl,$body);
			@WaterImg($rndFileName,'down');
	  }
	}
	$htd->Close();
	return $body;
}
//------------------------------
//获取一个远程图片
//------------------------------
function GetRemoteImage($url,$uid=0)
{
	$cfg_uploaddir = $GLOBALS['cfg_image_dir'];
	$cfg_basedir = $GLOBALS['cfg_basedir'];
	$revalues = Array();
	$ok = false;
	$htd = new DedeHttpDown();
	$htd->OpenUrl($url);
	$sparr = Array("image/pjpeg","image/jpeg","image/gif","image/png","image/x-png","image/wbmp");
	if(!in_array($htd->GetHead("content-type"),$sparr)){
		return "";
	}else{  	
  	$imgUrl = $cfg_uploaddir."/".strftime("%y%m%d",mytime());
	  $imgPath = $cfg_basedir.$imgUrl;
	  CreateDir($imgUrl);
  	$itype = $htd->GetHead("content-type");
		if($itype=="image/gif") $itype = ".gif";
		else if($itype=="image/png") $itype = ".png";
		else if($itype=="image/wbmp") $itype = ".bmp";
		else $itype = ".jpg";
		$rndname = dd2char($uid."_".strftime("%H%M%S",mytime()).mt_rand(1000,9999));
		$rndtrueName = $imgPath."/".$rndname.$itype;
		$fileurl = $imgUrl."/".$rndname.$itype;
  	$ok = $htd->SaveToBin($rndtrueName);
  	@WaterImg($rndtrueName,'down');
  	if($ok){
  	  $data = GetImageSize($rndtrueName);
  	  $revalues[0] = $fileurl;
	    $revalues[1] = $data[0];
	    $revalues[2] = $data[1];
	  }
  }
	$htd->Close();
	if($ok) return $revalues;
	else return "";
}
//------------------------------
//获取一个远程Flash文件
//------------------------------
function GetRemoteFlash($url,$uid=0)
{
	$cfg_uploaddir = $GLOBALS['media_dir'];
	$cfg_basedir = $GLOBALS['cfg_basedir'];
	$revalues = "";
	$sparr = "application/x-shockwave-flash";
	$htd = new DedeHttpDown();
	$htd->OpenUrl($url);
	if($htd->GetHead("content-type")!=$sparr){
		return "";
	}else{  	
  	$imgUrl = $cfg_uploaddir."/".strftime("%y%m%d",mytime());
	  $imgPath = $cfg_basedir.$imgUrl;
	  CreateDir($imgUrl);
  	$itype = ".swf";
		$milliSecond = $uid."_".strftime("%H%M%S",mytime());
		$rndFileName = $imgPath."/".$milliSecond.$itype;
		$fileurl = $imgUrl."/".$milliSecond.$itype;
  	$ok = $htd->SaveToBin($rndFileName);
  	if($ok) $revalues = $fileurl;
  }
	$htd->Close();
	return $revalues;
}

//---------------
//检测频道ID
//---------------
function CheckChannel($typeid,$channelid)
{
	 if($typeid==0) return true;
	 $dsql = new DedeSql(false);	  
	 $row = $dsql->GetOne("Select ispart,channeltype From #@__arctype where ID='$typeid' ");
	 $dsql->Close();
	 if($row['ispart']!=0 || $row['channeltype']!=$channelid) { return false; }
	 else { return true; }
}
//---------------
//检测档案权限
//---------------
function CheckArcAdmin($aid,$adminid)
{
	 $dsql = new DedeSql(false);
	 $row = $dsql->GetOne("Select adminid From #@__archives where ID='$aid' ");
	 $dsql->Close();
	 if($row['adminid']!=$adminid) return false;
	 else return true;
}
//---------------
//文档自动分页
//---------------
function SpLongBody($mybody,$spsize,$sptag)
{
  if(strlen($mybody)<$spsize) return $mybody;
  $bds = explode('<',$mybody);
  $npageBody = "";
  $istable = 0;
  $mybody = "";
  foreach($bds as $i=>$k)
  {
  	 if($i==0){ $npageBody .= $bds[$i]; continue;}
  	 $bds[$i] = "<".$bds[$i];
  	 if(strlen($bds[$i])>6){
  		  $tname = substr($bds[$i],1,5);
  		  if(strtolower($tname)=='table') $istable++;
  		  else if(strtolower($tname)=='/tabl') $istable--;
  		  if($istable>0){ $npageBody .= $bds[$i]; continue; }
  		  else $npageBody .= $bds[$i];
  	 }else{
  		  $npageBody .= $bds[$i];
  	 }
  	 if(strlen($npageBody)>$spsize){
  		  $mybody .= $npageBody.$sptag;
  		  $npageBody = "";
     }
  }
  if($npageBody!="") $mybody .= $npageBody;
  return $mybody;
}
//-----------------------
//创建指定ID的文档
//-----------------------
function MakeArt($aid,$checkLike=false)
{
	global $cfg_makeindex,$cfg_basedir,$cfg_templets_dir,$cfg_df_style;
	$arc = new Archives($aid);
  $reurl = $arc->MakeHtml();
  $arc->dsql = new DedeSql(false);
  $preRow = $arc->dsql->GetOne("Select ID From #@__archives where ID<$aid order by ID desc");
  $nextRow = $arc->dsql->GetOne("Select ID From #@__archives where ID>$aid order by ID asc");
  if(is_array($preRow)){
  	$arc->Close();
  	$arc = new Archives($preRow['ID']);
    $arc->MakeHtml();
  }
  if(is_array($nextRow)){
  	$arc->Close();
  	$arc = new Archives($nextRow['ID']);
    $arc->MakeHtml();
  }
  if($cfg_makeindex=='是'){
  	$pv = new PartView();
  	$row = $pv->dsql->GetOne("Select * From #@__homepageset");
  	$templet = str_replace("{style}",$cfg_df_style,$row['templet']);
  	$homeFile = dirname(__FILE__)."/../".$row['position'];
	  $homeFile = str_replace("\\","/",$homeFile);
	  $homeFile = str_replace("//","/",$homeFile);
	  $fp = fopen($homeFile,"w") or die("你指定的文件名有问题，无法创建文件");
	  fclose($fp);
	  $pv->SetTemplet($cfg_basedir.$cfg_templets_dir."/".$templet);
	  $pv->SaveToHtml($homeFile);
	  $pv->Close();
  }
  $arc->Close();
  return $reurl;
}
//------------------
//获得缩略图
//------------------
function GetDDImage($litpic,$picname,$isremote)
{
	global $cuserLogin,$cfg_ddimg_width,$cfg_ddimg_height,$cfg_basedir,$ddcfg_image_dir;
	$ntime = mytime();
	if(($litpic!='none'||$litpic!='ddfirst') &&
	 !empty($_FILES[$litpic]['tmp_name']) && is_uploaded_file($_FILES[$litpic]['tmp_name']))
	 //如果用户自行上传缩略图
  {
      $istype = 0;
      $sparr = Array("image/pjpeg","image/jpeg","image/gif","image/png");
      $_FILES[$litpic]['type'] = strtolower(trim($_FILES[$litpic]['type']));
      if(!in_array($_FILES[$litpic]['type'],$sparr)){
		    ShowMsg("上传的图片格式错误，请使用JPEG、GIF、PNG格式的其中一种！","-1");
		    exit();
	    }
      $savepath = $ddcfg_image_dir."/".strftime("%y%m%d",$ntime);
      CreateDir($savepath);
      $fullUrl = $savepath."/".dd2char(strftime("%H%M%S",$ntime).$cuserLogin->getUserID().mt_rand(1000,9999));
      
      if(strtolower($_FILES[$litpic]['type'])=="image/gif") $fullUrl = $fullUrl.".gif";
      else if(strtolower($_FILES[$litpic]['type'])=="image/png") $fullUrl = $fullUrl.".png";
      else $fullUrl = $fullUrl.".jpg";
      
      @move_uploaded_file($_FILES[$litpic]['tmp_name'],$cfg_basedir.$fullUrl);
	    $litpic = $fullUrl;
	    @ImageResize($cfg_basedir.$fullUrl,$cfg_ddimg_width,$cfg_ddimg_height);
  }else{
	    $picname = trim($picname);
	    if($isremote==1 && eregi("^http://",$picname)){
	  	    $litpic = $picname;
	  	    $ddinfos = GetRemoteImage($litpic,$cuserLogin->getUserID());
	  	    if(!is_array($ddinfos)) $litpic = "";
	  	    else{
	  		     $litpic = $ddinfos[0];
	  		     if($ddinfos[1] > $cfg_ddimg_width || $ddinfos[2] > $cfg_ddimg_height){
	  		   	    @ImageResize($cfg_basedir.$litpic,$cfg_ddimg_width,$cfg_ddimg_height);
	  		     }
	  	    }
	    }else{
	    	if($litpic=='ddfirst' && !eregi("^http://",$picname)){
	    		$oldpic = $cfg_basedir.$picname;
	    		$litpic = str_replace('.','_lit.',$picname);
	    		@ImageResize($oldpic,$cfg_ddimg_width,$cfg_ddimg_height,$cfg_basedir.$litpic);
	    		if(!is_file($cfg_basedir.$litpic)) $litpic = "";
	    	}
	    	else $litpic = $picname;
	    }
  }
  if($litpic=='litpic'||$litpic=='ddfirst') $litpic = "";
  return $litpic;
}
?>