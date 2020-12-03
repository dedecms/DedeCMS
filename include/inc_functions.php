<?
//拼音的缓冲数组
$pinyins = Array();
$g_ftpLink = false;
//客户端与服务时间差校正
function mytime(){
	 global $cfg_cli_time;
	 if(empty($cfg_cli_time)) $cfg_cli_time = 0;
	 $addtime = $cfg_cli_time * 3600;
	 return (time() + $cfg_cli_time);
}
//获得当前的脚本网址
function GetCurUrl(){
	if(!empty($_SERVER["REQUEST_URI"])){
		$scriptName = $_SERVER["REQUEST_URI"];
		$nowurl = $scriptName;
	}else{
		$scriptName = $_SERVER["PHP_SELF"];
		if(empty($_SERVER["QUERY_STRING"])) $nowurl = $scriptName;
		else $nowurl = $scriptName."?".$_SERVER["QUERY_STRING"];
	}
	return $nowurl;
}
//把全角数字转为半角数字
function GetAlabNum($fnum){
	$nums = array("０","１","２","３","４","５","６","７","８","９");
	$fnums = "0123456789";
	for($i=0;$i<=9;$i++) $fnum = str_replace($nums[$i],$fnums[$i],$fnum);
	$fnum = ereg_replace("[^0-9\.]|^0{1,}","",$fnum);
	if($fnum=="") $fnum=0;
	return $fnum;
}
//去除HTML标记符号
//function ClearHtml($html){
	//return trim(preg_replace("/[><]/","",$html));
//}
function Text2Html($txt){
	$txt = str_replace("  ","　",$txt);
	$txt = str_replace("<","&lt;",$txt);
	$txt = str_replace(">","&gt;",$txt);
	$txt = preg_replace("/[\r\n]{1,}/isU","<br/>\r\n",$txt);
	return $txt;
}
//获得HTML里的文本
function Html2Text($str){
  if(!isset($GLOBALS['__funString'])) require_once(dirname(__FILE__)."/inc/inc_fun_funString.php");
  return SpHtml2Text($str);
}
//清除HTML标记
function ClearHtml($str){
	$str = str_replace('<','&lt;',$str);
	$str = str_replace('>','&gt;',$str);
	return $str;
}
//中文截取把双字节字符也看作一个字符
function cnw_left($str,$len){
  return cnw_mid($str,0,$len);
}
function cnw_mid($str,$start,$slen){
  if(!isset($GLOBALS['__funString'])) require_once(dirname(__FILE__)."/inc/inc_fun_funString.php");
  return Spcnw_mid($str,$start,$slen);
}
//中文截取2，单字节截取模式
function cn_substr($str,$slen,$startdd=0){
	$restr = "";
	$c = "";
	$str_len = strlen($str);
	if($str_len < $startdd+1) return "";
	if($str_len < $startdd + $slen || $slen==0) $slen = $str_len - $startdd;
	$enddd = $startdd + $slen - 1;
	for($i=0;$i<$str_len;$i++)
	{
		if($startdd==0) $restr .= $c;
		else if($i > $startdd) $restr .= $c;
		
		if(ord($str[$i])>0x80){
			if($str_len>$i+1) $c = $str[$i].$str[$i+1];
			$i++;
		}
		else{	$c = $str[$i]; }

		if($i >= $enddd){
			if(strlen($restr)+strlen($c)>$slen) break;
			else{ $restr .= $c; break; }
		}
	}
	return $restr;
}
function cn_midstr($str,$start,$len){
	return cn_substr($str,$slen,$startdd);
}

function GetMkTime($dtime)
{
	if(!ereg("[^0-9]",$dtime)) return $dtime;
	$dt = Array(1970,1,1,0,0,0);
	$dtime = ereg_replace("[\r\n\t]|日|秒"," ",$dtime);
	$dtime = str_replace("年","-",$dtime);
	$dtime = str_replace("月","-",$dtime);
	$dtime = str_replace("时",":",$dtime);
	$dtime = str_replace("分",":",$dtime);
	$dtime = trim(ereg_replace("[ ]{1,}"," ",$dtime));
	$ds = explode(" ",$dtime);
	$ymd = explode("-",$ds[0]);
	if(isset($ymd[0])) $dt[0] = $ymd[0];
	if(isset($ymd[1])) $dt[1] = $ymd[1];
	if(isset($ymd[2])) $dt[2] = $ymd[2];
	if(strlen($dt[0])==2) $dt[0] = '20'.$dt[0];
	if(isset($ds[1])){
		$hms = explode(":",$ds[1]);
		if(isset($hms[0])) $dt[3] = $hms[0];
		if(isset($hms[1])) $dt[4] = $hms[1];
		if(isset($hms[2])) $dt[5] = $hms[2];
	}
  foreach($dt as $k=>$v){
  	$v = ereg_replace("^0{1,}","",trim($v));
  	if($v=="") $dt[$k] = 0;
  }
	$mt = @mktime($dt[3],$dt[4],$dt[5],$dt[1],$dt[2],$dt[0]);
	if($mt>0) return $mt;
	else return mytime();
}

function SubDay($ntime,$ctime){
	$dayst = 3600 * 24;
	$cday = ceil(($ntime-$ctime)/$dayst);
	return $cday;
}

function AddDay($ntime,$aday){
	$dayst = 3600 * 24;
	$oktime = $ntime + ($aday * $dayst);
	return $oktime;
}

function GetDateTimeMk($mktime){
	if($mktime==""||ereg("[^0-9]",$mktime)) return "";
	return strftime("%Y-%m-%d %H:%M:%S",$mktime);
}

function GetDateMk($mktime){
	if($mktime==""||ereg("[^0-9]",$mktime)) return "";
	return strftime("%Y-%m-%d",$mktime);
}

function GetIP(){
	if(!empty($_SERVER["HTTP_CLIENT_IP"])) $cip = $_SERVER["HTTP_CLIENT_IP"];
	else if(!empty($_SERVER["HTTP_X_FORWARDED_FOR"])) $cip = $_SERVER["HTTP_X_FORWARDED_FOR"];
	else if(!empty($_SERVER["REMOTE_ADDR"])) $cip = $_SERVER["REMOTE_ADDR"];
	else $cip = "无法获取！";
	return $cip;
}
//获取一串中文字符的拼音 ishead=0 时，输出全拼音 ishead=1时，输出拼音首字母
function GetPinyin($str,$ishead=0,$isclose=1){
	if(!isset($GLOBALS['__funAdmin'])) require_once(dirname(__FILE__)."/inc/inc_fun_funAdmin.php");
  return SpGetPinyin($str,$ishead,$isclose);
}

function GetNewInfo(){
	if(!isset($GLOBALS['__funAdmin'])) require_once(dirname(__FILE__)."/inc/inc_fun_funAdmin.php");
  return SpGetNewInfo();
}

function ShowMsg($msg,$gourl,$onlymsg=0,$limittime=0){
		$htmlhead  = "<html>\r\n<head>\r\n<title>提示信息</title>\r\n<meta http-equiv=\"Content-Type\" content=\"text/html; charset=gb2312\" />\r\n";
		$htmlhead .= "<base target='_self'/>\r\n</head>\r\n<body leftmargin='0' topmargin='0'>\r\n<center>\r\n<script>\r\n";
		$htmlfoot  = "</script>\r\n</center>\r\n</body>\r\n</html>\r\n";
		
		if($limittime==0) $litime = 1000;
		else $litime = $limittime;
		
		if($gourl=="-1"){
			if($limittime==0) $litime = 5000;
			$gourl = "javascript:history.go(-1);";
		}
		
		if($gourl==""||$onlymsg==1){
			$msg = "<script>alert(\"".str_replace("\"","“",$msg)."\");</script>";
		}else{
			$func = "      var pgo=0;
      function JumpUrl(){
        if(pgo==0){ location='$gourl'; pgo=1; }
      }\r\n";
			$rmsg = $func;
			$rmsg .= "document.write(\"<br/><div style='width:400px;padding-top:4px;height:24;font-size:10pt;border-left:1px solid #cccccc;border-top:1px solid #cccccc;border-right:1px solid #cccccc;background-color:#DBEEBD;'>DEDECMS 提示信息！</div>\");\r\n";
			$rmsg .= "document.write(\"<div style='width:400px;height:100;font-size:10pt;border:1px solid #cccccc;background-color:#F4FAEB'><br/><br/>\");\r\n";
			$rmsg .= "document.write(\"".str_replace("\"","“",$msg)."\");\r\n";
			$rmsg .= "document.write(\"";
			if($onlymsg==0){
				if($gourl!="javascript:;" && $gourl!=""){ $rmsg .= "<br/><br/><a href='".$gourl."'>如果你的浏览器没反应，请点击这里...</a>"; }
				$rmsg .= "<br/><br/></div>\");\r\n";
				if($gourl!="javascript:;" && $gourl!=""){ $rmsg .= "setTimeout('JumpUrl()',$litime);"; }
			}else{ $rmsg .= "<br/><br/></div>\");\r\n"; }
			$msg  = $htmlhead.$rmsg.$htmlfoot;
		}		
		echo $msg;
}

function ExecTime(){ 
	$time = explode(" ", microtime());
	$usec = (double)$time[0]; 
	$sec = (double)$time[1]; 
	return $sec + $usec; 
}

function GetEditor($fname,$fvalue,$nheight="350",$etype="Basic",$gtype="print",$isfullpage="false"){
	if(!isset($GLOBALS['__funAdmin'])) require_once(dirname(__FILE__)."/inc/inc_fun_funAdmin.php");
  return SpGetEditor($fname,$fvalue,$nheight,$etype,$gtype,$isfullpage);
}
//获得指定位置模板字符串
function GetTemplets($filename){
	if(file_exists($filename)){
     $fp = fopen($filename,"r");
     $rstr = fread($fp,filesize($filename));
     fclose($fp);
     return $rstr;
	}else{ return ""; }
}
function GetSysTemplets($filename){
	return GetTemplets($GLOBALS['cfg_basedir'].$GLOBALS['cfg_templets_dir'].'/system/'.$filename);
}
//给变量赋默认值
function AttDef($oldvar,$nv){
 	if(empty($oldvar)) return $nv;
 	else return $oldvar;
}
//把符合规则的数字转为字符
function dd2char($dd){
  $slen = strlen($dd);
  $okdd = "";
  for($i=0;$i<$slen;$i++){
  	if(isset($dd[$i+1])){
  		$n = $dd[$i].$dd[$i+1];
  		if(($n>96 && $n<123)||($n>64 && $n<91)){ $okdd .= chr($n); $i++; }
  		else $okdd .= $dd[$i];
    }else $okdd .= $dd[$i];
  }
  return $okdd;
}
//按默认参数设置一个Cookie
function PutCookie($key,$value,$kptime,$pa="/"){
	 global $cfg_cookie_encode;
	 setcookie($key,$value,time()+$kptime,$pa);
	 setcookie($key.'__ckMd5',substr(md5($cfg_cookie_encode.$value),0,16),time()+$kptime,$pa);
}
//使Cookie失效
function DropCookie($key){
  setcookie($key,"",time()-360000,"/");
	setcookie($key.'__ckMd5',"",time()-360000,"/");
}
//获得一个cookie值
function GetCookie($key){
	 global $cfg_cookie_encode;
	 if( !isset($_COOKIE[$key]) || !isset($_COOKIE[$key.'__ckMd5']) ) return '';
	 else{
	   if($_COOKIE[$key.'__ckMd5']!=substr(md5($cfg_cookie_encode.$_COOKIE[$key]),0,16)) return '';
	   else return $_COOKIE[$key];
	 }
}
//获得验证码的值
function GetCkVdValue(){
	@session_start();
	if(isset($_SESSION["dd_ckstr"])) $ckvalue = $_SESSION["dd_ckstr"];
	else $ckvalue = '';
	//DropCookie("dd_ckstr");
	return $ckvalue;
}
//用FTP创建一个目录
function FtpMkdir($truepath,$mmode,$isMkdir=true){
	global $cfg_basedir,$cfg_ftp_root,$g_ftpLink;
	OpenFtp();
	$ftproot = ereg_replace($cfg_ftp_root.'$','',$cfg_basedir);
	$mdir = ereg_replace('^'.$ftproot,'',$truepath);
	if($isMkdir) ftp_mkdir($g_ftpLink,$mdir);
	return ftp_site($g_ftpLink,"chmod $mmode $mdir");
}
//用FTP改变一个目录的权限
function FtpChmod($truepath,$mmode){
	return FtpMkdir($truepath,$mmode,false);
}
//打开FTP连接
function OpenFtp(){
	global $cfg_basedir,$cfg_ftp_host,$cfg_ftp_port;
	global $cfg_ftp_user,$cfg_ftp_pwd,$cfg_ftp_root,$g_ftpLink;
	if(!$g_ftpLink){
		if($cfg_ftp_host==""){
		  echo "由于你的站点的PHP配置存在限制，程序尝试用FTP进行目录操作，你必须在后台指定FTP相关的变量！";
		  exit();
	  }
		$g_ftpLink = ftp_connect($cfg_ftp_host,$cfg_ftp_port);
	  if(!$g_ftpLink){ echo "连接FTP失败！"; exit(); }
	  if(!ftp_login($g_ftpLink,$cfg_ftp_user,$cfg_ftp_pwd)){ echo "登陆FTP失败！"; exit(); }
	}
}
//关闭FTP连接
function CloseFtp(){
	global $g_ftpLink;
	if($g_ftpLink) @ftp_quit($g_ftpLink);
}
//通用的创建目录的函数
function MkdirAll($truepath,$mmode){
	global $cfg_ftp_mkdir,$isSafeMode; 
	if($isSafeMode||$cfg_ftp_mkdir=='是'){ return FtpMkdir($truepath,$mmode); }
	else{
		  if(!file_exists($truepath)){
		  	 mkdir($truepath,0777);
		  	 chmod($truepath,0777);
		  	 return true;
		  }else{
		  	return true;
		  }
  }
}
//通用的更改目录或文件权限的函数
function ChmodAll($truepath,$mmode){
	global $cfg_ftp_mkdir,$isSafeMode; 
	if($isSafeMode||$cfg_ftp_mkdir=='是'){ return FtpChmod($truepath,$mmode); }
	else{ return chmod($truepath,'0'.$mmode); }
}

//创建多层次的目录
function CreateDir($spath,$siterefer="",$sitepath=""){
	if(!isset($GLOBALS['__funAdmin'])) require_once(dirname(__FILE__)."/inc/inc_fun_funAdmin.php");
  return SpCreateDir($spath,$siterefer,$sitepath);
}

//过滤用户输入用于查询的字符串
function StringFilterSearch($str,$isint=0){
	return $str;
}

?>