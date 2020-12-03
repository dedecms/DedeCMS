<?
if(!isset($registerGlobals)){ require_once(dirname(__FILE__)."/../../include/config_base.php"); }
require_once(dirname(__FILE__)."/../../include/pub_httpdown.php");
require_once(dirname(__FILE__)."/../../include/inc_archives_view.php");
//---------------------------
//获得文章body里的外部资源
//---------------------------
function GetCurContent($body)
{
	$cfg_uploaddir = $GLOBALS['cfg_image_dir'];
	$cfg_basedir = $GLOBALS['cfg_basedir'];
	$htd = new DedeHttpDown();
	
	$basehost = "http://".$_SERVER["HTTP_HOST"];
  $body = str_replace(strtolower($basehost),"",$body);
  $body = str_replace(strtoupper($basehost),"",$body);
  
	$img_array = array();
	preg_match_all("/(src|SRC)=[\"|'| ]{0,}(http:\/\/(.*)\.(gif|jpg|jpeg|bmp|png))/isU",$body,$img_array);
	$img_array = array_unique($img_array[2]);
	
	$imgUrl = $cfg_uploaddir."/".strftime("%Y%m%d",time());
	$imgPath = $cfg_basedir.$imgUrl;
	if(!is_dir($imgPath."/")) @mkdir($imgPath,0777);
	$milliSecond = strftime("%H%M%S",time());
	
	foreach($img_array as $key=>$value)
	{
		if(eregi("^http://",$value) && !eregi($basehost,$value))
		{
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
		   }
		}//
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
	$revalues = "";
	$htd = new DedeHttpDown();
	$htd->OpenUrl($url);
	
	$sparr = Array("image/pjpeg","image/jpeg","image/gif","image/png");
	if(!in_array($htd->GetHead("content-type"),$sparr)){
		return "";
	}else
	{  	
  	$imgUrl = $cfg_uploaddir."/".strftime("%Y%m%d",time());
	  $imgPath = $cfg_basedir.$imgUrl;
	  
	  CreateDir($imgUrl);
  	
  	$itype = $htd->GetHead("content-type");
		if($itype=="image/gif") $itype = ".gif";
		else if($itype=="image/png") $itype = ".png";
		else $itype = ".jpg";
		$milliSecond = $uid."_".strftime("%H%M%S",time());
		
		$rndFileName = $imgPath."/".$milliSecond.$itype;
		$fileurl = $imgUrl."/".$milliSecond.$itype;
  	
  	$ok = $htd->SaveToBin($rndFileName);
  	if($ok)
  	{
  	  $info = "";
  	  $data = GetImageSize($rndFileName,$info);
  	  $revalues[0] = $fileurl;
	    $revalues[1] = $data[0];
	    $revalues[2] = $data[1];
	  }
  }
	$htd->Close();
	return $revalues;
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
	}else
	{  	
  	$imgUrl = $cfg_uploaddir."/".strftime("%Y%m%d",time());
	  $imgPath = $cfg_basedir.$imgUrl;
	  CreateDir($imgUrl);
  	$itype = ".swf";
		$milliSecond = $uid."_".strftime("%H%M%S",time());
		$rndFileName = $imgPath."/".$milliSecond.$itype;
		$fileurl = $imgUrl."/".$milliSecond.$itype;
  	$ok = $htd->SaveToBin($rndFileName);
  	if($ok) $revalues = $fileurl;
  }
	$htd->Close();
	return $revalues;
}
//-----------------------
//创建指定ID的文档
//-----------------------
function MakeArt($aid,$checkLike=false)
{
	$arc = new Archives($aid);
  $reurl = $arc->MakeHtml();
  $arc->Close();
  return $reurl;
}

?>
