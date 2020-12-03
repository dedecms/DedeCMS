<?php 
require_once(DEDEADMIN."/../include/pub_httpdown.php");
require_once(DEDEADMIN."/../include/inc_photograph.php");
require_once(DEDEADMIN."/../include/pub_oxwindow.php");
require_once(DEDEADMIN."/../include/inc_tag_functions.php");
require_once(DEDEADMIN."/../include/inc_custom_fields.php");
//---------------------------
//获得HTML里的外部资源，针对图集
//---------------------
function GetCurContentAlbum($body,$rfurl,&$firstdd)
{
	global $cfg_multi_site,$cfg_basehost,$ddmaxwidth,$cfg_basedir;
	include_once(DEDEADMIN."/../include/pub_collection_functions.php");
	if(empty($ddmaxwidth)) $ddmaxwidth = 150;
	$rsimg = '';
	$cfg_uploaddir = $GLOBALS['cfg_image_dir'];
	$cfg_basedir = $GLOBALS['cfg_basedir'];
	$basehost = "http://".$_SERVER["HTTP_HOST"];
  if($cfg_multi_site == 'N'){
    $body = str_replace(strtolower($basehost),"",$body);
    $body = str_replace(strtoupper($basehost),"",$body);
  }else{
  	if($cfg_basehost!=$basehost){
  		$body = str_replace(strtolower($basehost),$cfg_basehost,$body);
  		$body = str_replace(strtoupper($basehost),$cfg_basehost,$body);
  	}
  }
	$img_array = array();
	preg_match_all("/(src|SRC)=[\"|'| ]{0,}(http:\/\/(.*)\.(gif|jpg|jpeg|png))/isU",$body,$img_array);
	$img_array = array_unique($img_array[2]);
	
	$imgUrl = $cfg_uploaddir."/".strftime("%y%m%d",mytime());
	$imgPath = $cfg_basedir.$imgUrl;
	if(!is_dir($imgPath."/")){
		MkdirAll($imgPath,$GLOBALS['cfg_dir_purview']);
		CloseFtp();
	}
	$milliSecond = strftime("%H%M%S",mytime());
	
	foreach($img_array as $key=>$value)
	{
		if(eregi($basehost,$value)) continue;
		if($cfg_basehost!=$basehost && eregi($cfg_basehost,$value)) continue;
		if(!eregi("^http://",$value)) continue;
		
		$value = trim($value);
		$itype =  substr($value,0,-4);
		if(!eregi("gif|jpg|jpeg|png",$itype)) $itype = ".jpg";
		
		$rndFileName = $imgPath."/".$milliSecond.$key.$itype;
		$iurl = $imgUrl."/".$milliSecond.$key.$itype;
		//下载并保存文件
		//$rs = $htd->SaveToBin($rndFileName);
		$rs = DownImageKeep($value,$rfurl,$rndFileName,"",0,30);
		if($rs){
			$litpicname = GetImageMapDD($iurl,$ddmaxwidth);
			if(empty($firstdd) && !empty($litpicname)){
				$firstdd = $litpicname;
				if(!file_exists($cfg_basedir.$firstdd)) $firstdd = $iurl;
			}
			@WaterImg($rndFileName,'down');
			$info = '';
			$imginfos = GetImageSize($rndFileName,$info);
			$rsimg .= "{dede:img ddimg='$litpicname' text='' width='".$imginfos[0]."' height='".$imginfos[1]."'} $iurl {/dede:img}\r\n";
	  }
	}
	return $rsimg;
}

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
  if($cfg_multi_site == 'N'){
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
		MkdirAll($imgPath,$GLOBALS['cfg_dir_purview']);
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
$GLOBALS['_i'] = 0;
function GetRemoteImage($url,$uid=0)
{
	global $cuserLogin,$_i;
	$_i++;
	if(empty($uid)) $uid = $cuserLogin->getUserID();
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
  	$imgUrl = $cfg_uploaddir."/".strftime("%Y%m",mytime());
	  $imgPath = $cfg_basedir.$imgUrl;
	  CreateDir($imgUrl);
  	$itype = $htd->GetHead("content-type");
		if($itype=="image/gif") $itype = ".gif";
		else if($itype=="image/png") $itype = ".png";
		else if($itype=="image/wbmp") $itype = ".bmp";
		else $itype = ".jpg";
		
		//$rndname = dd2char($uid."_".strftime("%H%M%S",mytime()).mt_rand(1000,9999));
		$rndname = strftime("%d",$ntime).dd2char(strftime("%H%M%S",$ntime).'0'.$uid.'0'.mt_rand(1000,9999)).'-'.$_i;
		
		$rndtrueName = $imgPath."/".$rndname.$itype;
		$fileurl = $imgUrl."/".$rndname.$itype;
		
  	$ok = $htd->SaveToBin($rndtrueName);
  	
  	//加水印
  	@WaterImg($rndtrueName,'down');
  	if($ok){
  	  $info = '';
  	  $data = GetImageSize($rndtrueName,$info);
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
	 if($row['adminid']!=$adminid) return false;
	 else return true;
}
//---------------
//文档自动分页
//---------------
function SpLongBody(&$mybody,$spsize,$sptag)
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
	global $cfg_makeindex,$cfg_basedir,$cfg_templets_dir,$cfg_df_style,$cfg_up_prenext,$typeid;
	include_once(DEDEADMIN."/../include/inc_archives_view.php");
	$arc = new Archives($aid);
  $reurl = $arc->MakeHtml();
  //更新上下篇文章
  if($cfg_up_prenext=='Y' && !empty($typeid))
  {
     $preRow =  $arc->dsql->GetOne("Select ID From `{$arc->MainTable}` where ID<$aid And arcrank>-1 And typeid='$typeid' order by ID desc");
     $nextRow = $arc->dsql->GetOne("Select ID From `{$arc->MainTable}` where ID<$aid And arcrank>-1 And typeid='$typeid' order by ID asc");
    if(is_array($preRow)){
  	   $arc = new Archives($preRow['ID']);
       $arc->MakeHtml();
    }
    if(is_array($nextRow)){
  	   $arc = new Archives($nextRow['ID']);
       $arc->MakeHtml();
    }
  }
  //更新主页
  if($cfg_makeindex=='Y')
  {
  	$pv = new PartView();
  	$row = $pv->dsql->GetOne("Select * From #@__homepageset");
  	$templet = str_replace("{style}",$cfg_df_style,$row['templet']);
  	$homeFile = dirname(__FILE__)."/../".$row['position'];
	  $homeFile = str_replace("\\","/",$homeFile);
	  $homeFile = str_replace("//","/",$homeFile);
	  $fp = fopen($homeFile,"w") or die("主页文件：{$homeFile} 没有写权限！");
	  fclose($fp);
	  $pv->SetTemplet($cfg_basedir.$cfg_templets_dir."/".$templet);
	  $pv->SaveToHtml($homeFile);
  }
  return $reurl;
}
//---------------------------
//上传缩略图
//--------------------------
/*
参数一
litpic 默认为上传表单的名称
值为 none\ddfirst 强制不查检上传
值为 ddfirst 则强制把已存在的本地图生成缩略图
参数二
picname 手工填写的图片路径
参数三
isremote 是否下载远程图片 0 为不下载, 1为下载到本地
*/
function GetDDImage($litpic,$picname,$isremote,$ntitle='')
{
	global $cuserLogin,$cfg_ddimg_width,$cfg_ddimg_height;
	global $cfg_basedir,$ddcfg_image_dir,$title,$dsql;
	$ntime = mytime();
	$saveinfo = false;
	if($ntitle!='') $title = $ntitle;
	$picname = trim($picname);
	
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
	    
	    if(!empty($picname) && !eregi("^http://",$picname) && file_exists($cfg_basedir.$picname) ){
	    	 if(!is_object($dsql)) $dsql = new DedeSql();
         $dsql->ExecuteNoneQuery("Delete From #@__uploads where url like '$picname' ");
	    	 $fullUrl = eregi_replace("\.([a-z]*)$","",$picname);
	    }else{
	    	  $savepath = $ddcfg_image_dir."/".strftime("%y%m%d",$ntime);
          CreateDir($savepath);
          $fullUrl = $savepath."/".dd2char(strftime("%H%M%S",$ntime).$cuserLogin->getUserID().mt_rand(1000,9999));
	    }
      
      if(strtolower($_FILES[$litpic]['type'])=="image/gif") $fullUrl = $fullUrl.".gif";
      else if(strtolower($_FILES[$litpic]['type'])=="image/png") $fullUrl = $fullUrl.".png";
      else $fullUrl = $fullUrl.".jpg";
      
      @move_uploaded_file($_FILES[$litpic]['tmp_name'],$cfg_basedir.$fullUrl);
	    $litpic = $fullUrl;
	    
	    @ImageResize($cfg_basedir.$fullUrl,$cfg_ddimg_width,$cfg_ddimg_height);
	    
	    $saveinfo = true;
	    
  }else{
	    if($picname=='') return '';
	    //远程缩略
	    if($isremote==1 && eregi("^http://",$picname)){
	  	    $ddinfos = GetRemoteImage($picname,$cuserLogin->getUserID());
	  	    if(!is_array($ddinfos)) $litpic = "";
	  	    else{
	  		     $litpic = $ddinfos[0];
	  		     if($ddinfos[1] > $cfg_ddimg_width || $ddinfos[2] > $cfg_ddimg_height){
	  		   	    @ImageResize($cfg_basedir.$litpic,$cfg_ddimg_width,$cfg_ddimg_height);
	  		     }
	  	    }
	  	    $saveinfo = true;
	    }
	    //本地缩略
	    else{
	    	 //本地大图强制生成本地图缩略
	    	 if($litpic=='ddfirst' && !eregi("^http://",$picname)){
	    		  $oldpic = $cfg_basedir.$picname;
	    		  if(!eregi('_lit',$litpic)){
	    		  	$litpic = str_replace('.','_lit.',$picname);
	    		  	$saveinfo = true;
	    		  }
	    		  @ImageResize($oldpic,$cfg_ddimg_width,$cfg_ddimg_height,$cfg_basedir.$litpic);
	    	 }else{ 
	    		  $litpic = $picname;
	    	 }
	    }
  }

  $imgfile = $cfg_basedir.$litpic;
  if($saveinfo && is_file($imgfile) && $litpic!=''){
		$info = "";
		$imginfos = GetImageSize($imgfile,$info);
		//把新上传的图片信息保存到媒体文档管理档案中
		$inquery = "
        INSERT INTO #@__uploads(title,url,mediatype,width,height,playtime,filesize,uptime,adminid,memberid) 
        VALUES ('{$title} 缩略图','$litpic','1','".$imginfos[0]."','".$imginfos[1]."','0','".filesize($imgfile)."','".mytime()."','".$cuserLogin->getUserID()."','0');
    ";
     $dsql = new DedeSql();
     $dsql->ExecuteNoneQuery($inquery);
	}


  return $litpic;
}

//检测栏目是否设置了浏览权限
function GetCoRank($arcrank,$typeid){
	 $dsql = new DedeSql(false);	  
	 $row = $dsql->GetOne("Select corank From #@__arctype where ID='$typeid' ");
	 if($row['corank']!=0) return $row['corank'];
	 else return $arcrank;
}


//图集里大图的小图
function GetImageMapDD($filename,$ddm,$oldname=''){
	 if($oldname!='' && !eregi("^http://",$oldname)){
	 	 $ddpicok = $oldname;
	 }else{
	 	 $ddn = substr($filename,-3);
	   $ddpicok = ereg_replace("\.".$ddn."$","-lp.".$ddn,$filename);
	 }
	 $toFile = $GLOBALS['cfg_basedir'].$ddpicok;
	 ImageResize($GLOBALS['cfg_basedir'].$filename,$ddm,300,$toFile);
	 return $ddpicok;
}

//------------------------
//上传一个未经处理的图片
//------------------------
/*
//参数一 upname 上传框名称
//参数二 handurl 手工填写的网址
//参数三 ddisremote 是否下载远程图片 0 不下, 1 下载
//参数四 ntitle 注解文字 如果表单有 title 字段可不管
*/
function UploadOneImage($upname,$handurl='',$ddisremote=1,$ntitle='')
{
	
	global $cuserLogin,$cfg_basedir,$cfg_image_dir,$dsql,$title;
	if($ntitle!='') $title = $ntitle; 
	$ntime = mytime();
	$filename = '';
	$isrm_up = false;
	$handurl = trim($handurl);
	//如果用户自行上传了图片
	if(!empty($_FILES[$upname]['tmp_name']) && is_uploaded_file($_FILES[$upname]['tmp_name']))
  {
      $istype = 0;
      $sparr = Array("image/pjpeg","image/jpeg","image/gif","image/png");
      $_FILES[$upname]['type'] = strtolower(trim($_FILES[$upname]['type']));
      if(!in_array($_FILES[$upname]['type'],$sparr)){
		     ShowMsg("上传的图片格式错误，请使用JPEG、GIF、PNG格式的其中一种！","-1");
		     exit();
	    }
      
      
      if(!empty($handurl) && !eregi("^http://",$handurl) && file_exists($cfg_basedir.$handurl) ){
	    	 if(!is_object($dsql)) $dsql = new DedeSql();
         $dsql->ExecuteNoneQuery("Delete From #@__uploads where url like '$handurl' ");
	    	 $fullUrl = eregi_replace("\.([a-z]*)$","",$handurl);
	    }else{
	    	 $savepath = $cfg_image_dir."/".strftime("%Y-%m",$ntime);
         CreateDir($savepath);
         $fullUrl = $savepath."/".strftime("%d",$ntime).dd2char(strftime("%H%M%S",$ntime).'0'.$cuserLogin->getUserID().'0'.mt_rand(1000,9999));
	    }
      
      if(strtolower($_FILES[$upname]['type'])=="image/gif") $fullUrl = $fullUrl.".gif";
      else if(strtolower($_FILES[$upname]['type'])=="image/png") $fullUrl = $fullUrl.".png";
      else $fullUrl = $fullUrl.".jpg";
      
      //保存
      @move_uploaded_file($_FILES[$upname]['tmp_name'],$cfg_basedir.$fullUrl);
	    $filename = $fullUrl;

	    //水印
	    @WaterImg($imgfile,'up');
	    $isrm_up = true;
	    
  }
  //远程或选择本地图片
  else{
	    if($handurl=='') return '';
	    //远程图片并要求本地化
	    if($isremote==1 && eregi("^http://",$handurl)){
	  	   $ddinfos = GetRemoteImage($handurl,$cuserLogin->getUserID());
	  	   if(!is_array($ddinfos)) $litpic = "";
	  	   else $filename = $ddinfos[0];
	  	   $isrm_up = true;
	    //本地图片或远程不要求本地化
	    }else{
	    	$filename = $handurl;
	    }
  }
  $imgfile = $cfg_basedir.$filename;
  if(is_file($imgfile) && $isrm_up && $filename!=''){
		$info = "";
		$imginfos = GetImageSize($imgfile,$info);
		//把新上传的图片信息保存到媒体文档管理档案中
		$inquery = "
        INSERT INTO #@__uploads(title,url,mediatype,width,height,playtime,filesize,uptime,adminid,memberid) 
        VALUES ('$title','$filename','1','".$imginfos[0]."','".$imginfos[1]."','0','".filesize($imgfile)."','".mytime()."','".$cuserLogin->getUserID()."','0');
    ";
     $dsql = new DedeSql(false);
     $dsql->ExecuteNoneQuery($inquery);
	}
  return $filename;
}

//------------------
//取第一个图片为缩略图
//------------------
function GetDDImgFromBody(&$body)
{
	$litpic = '';
	preg_match_all("/(src|SRC)=[\"|'| ]{0,}(.*\.(gif|jpg|jpeg|bmp|png))/isU",$body,$img_array);
	$img_array = array_unique($img_array[2]);
	if(count($img_array)>0){
		$picname = preg_replace("/[\"|'| ]{1,}/","",$img_array[0]);
		if(ereg("_lit\.",$picname)) $litpic = $picname;
  	else $litpic = GetDDImage('ddfirst',$picname,0);
	}
	return $litpic;
}

//获得一个附加表单
//-----------------------------
function GetFormItemA($ctag)
{
	return GetFormItem($ctag,'admin');
}
//---------------------------
//处理不同类型的数据
//---------------------------
function GetFieldValueA($dvalue,$dtype,$aid=0,$job='add',$addvar='')
{
	return GetFieldValue($dvalue,$dtype,$aid,$job,$addvar,'admin');
}
//获得带值的表单(编辑时用)
//-----------------------------
function GetFormItemValueA($ctag,$fvalue)
{
	return GetFormItemValue($ctag,$fvalue,'admin');
}

//载入自定义表单(用于发布)
function PrintAutoFieldsAdd(&$fieldset,$loadtype='all')
{
   $dtp = new DedeTagParse();
	 $dtp->SetNameSpace("field","<",">");
   $dtp->LoadSource($fieldset);
   $dede_addonfields = "";
   if(is_array($dtp->CTags))
   {
      foreach($dtp->CTags as $tid=>$ctag)
			{
        	if($loadtype!='autofield' 
        	|| ($loadtype=='autofield' && $ctag->GetAtt('autofield')==1) )
        	{
        			$dede_addonfields .= ( $dede_addonfields=="" ? $ctag->GetName().",".$ctag->GetAtt('type') : ";".$ctag->GetName().",".$ctag->GetAtt('type') );
              echo  GetFormItemA($ctag);
        	}
      }
  }
  echo "<input type='hidden' name='dede_addonfields' value=\"".$dede_addonfields."\">\r\n";
}

//载入自定义表单(用于编辑)
function PrintAutoFieldsEdit(&$fieldset,&$fieldValues,$loadtype='all')
{
   $dtp = new DedeTagParse();
	 $dtp->SetNameSpace("field","<",">");
   $dtp->LoadSource($fieldset);
   $dede_addonfields = "";
   if(is_array($dtp->CTags))
   {
      foreach($dtp->CTags as $tid=>$ctag)
			{
        if($loadtype!='autofield' 
        || ($loadtype=='autofield' && $ctag->GetAtt('autofield')==1) )
        {
             $dede_addonfields .= ( $dede_addonfields=='' ? $ctag->GetName().",".$ctag->GetAtt('type') : ";".$ctag->GetName().",".$ctag->GetAtt('type') );
             echo GetFormItemValueA($ctag,$fieldValues[$ctag->GetName()]);
        }
      }
  }
  echo "<input type='hidden' name='dede_addonfields' value=\"".$dede_addonfields."\">\r\n";
}

?>