<?php
require_once(dirname(__FILE__).'/pub_charset.php');
//拼音的缓冲数组
$pinyins = Array();
$g_ftpLink = false;
//客户端与服务时间差校正
function mytime()
{
	return time();
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
	$fnum = utf82gb($fnum);
	$nums = array('０','１','２','３','４','５','６','７','８','９','．','－','＋','：');
	$fnums = array('0','1',  '2','3',  '4','5',  '6', '7','8',  '9','.',  '-', '+',':');
	$fnlen = count($fnums);
	for($i=0;$i<$fnlen;$i++) $fnum = str_replace($nums[$i],$fnums[$i],$fnum);
	$slen = strlen($fnum);
	$oknum = '';
	for($i=0;$i<$slen;$i++){
		if(ord($fnum[$i]) > 0x80) $i++;
		else $oknum .= $fnum[$i];
	}
	if($oknum=="") $oknum=0;
	return $oknum;
}
//文本转HTML
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
	$str = Html2Text($str);
	$str = str_replace('<','&lt;',$str);
	$str = str_replace('>','&gt;',$str);
	return $str;
}
//中文截取把双字节字符也看作一个字符
function cnw_left($str,$len){
   $str =  utf82gb($str);
	return gb2utf8(cnw_mid($str,0,$len));
}
//此函数在UTF8版中不能直接调用
function cnw_mid($str,$start,$slen){
  if(!isset($GLOBALS['__funString'])) require_once(dirname(__FILE__)."/inc/inc_fun_funString.php");
  return Spcnw_mid($str,$start,$slen);
}
//中文截取2，单字节截取模式
function cn_substr($str,$slen,$startdd=0){
  $str =  utf82gb($str);
  return gb2utf8(cn_substrGb($str,$slen,$startdd));
}
//此函数在UTF8版中不能直接调用
function cn_substrGb($str,$slen,$startdd=0){
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
	$str = utf82gb($str);
	if(!isset($GLOBALS['__funAdmin'])) require_once(dirname(__FILE__)."/inc/inc_fun_funAdmin.php");
  return SpGetPinyin($str,$ishead,$isclose);
}

function GetNewInfo(){
	if(!isset($GLOBALS['__funAdmin'])) require_once(dirname(__FILE__)."/inc/inc_fun_funAdmin.php");
  return SpGetNewInfo();
}

function ShowMsg($msg,$gourl,$onlymsg=0,$limittime=0)
{
	  global $dsql;
		$htmlhead  = "<html>\r\n<head>\r\n<title>DedeCms 系统提示</title>\r\n<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />\r\n";
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
			$rmsg .= "document.write(\"<br/><div style='width:400px;padding-top:4px;height:24;font-size:10pt;border-left:1px solid #b9df92;border-top:1px solid #b9df92;border-right:1px solid #b9df92;background-color:#def5c2;'>DedeCms 提示信息：</div>\");\r\n";
			$rmsg .= "document.write(\"<div style='width:400px;height:100;font-size:10pt;border:1px solid #b9df92;background-color:#f9fcf3'><br/><br/>\");\r\n";
			$rmsg .= "document.write(\"".str_replace("\"","“",$msg)."\");\r\n";
			$rmsg .= "document.write(\"";
			if($onlymsg==0){
				if($gourl!="javascript:;" && $gourl!=""){ $rmsg .= "<br/><br/><a href='".$gourl."'>如果你的浏览器没反应，请点击这里...</a>"; }
				$rmsg .= "<br/><br/></div>\");\r\n";
				if($gourl!="javascript:;" && $gourl!=""){ $rmsg .= "setTimeout('JumpUrl()',$litime);"; }
			}else{ $rmsg .= "<br/><br/></div>\");\r\n"; }
			$msg  = $htmlhead.$rmsg.$htmlfoot;
		}
		if(isset($dsql) && is_object($dsql)) @$dsql->Close();
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
	 global $cfg_cookie_encode,$cfg_pp_isopen,$cfg_basehost;
	 if(empty($cfg_pp_isopen)
	  ||!ereg("\.",$cfg_basehost)||!ereg("[a-zA-Z]",$cfg_basehost))
	 {
	   setcookie($key,$value,mytime()+$kptime,$pa);
	   setcookie($key.'ckMd5',substr(md5($cfg_cookie_encode.$value),0,16),mytime()+$kptime,$pa);
	 }else
	 {
	 	 $dm = eregi_replace("http://([^\.]*)\.","",$cfg_basehost);
	 	 $dm = ereg_replace("/(.*)","",$dm);
	 	 setcookie($key,$value,mytime()+$kptime,$pa,$dm);
	   setcookie($key.'ckMd5',substr(md5($cfg_cookie_encode.$value),0,16),mytime()+$kptime,$pa,$dm);
	 }
}
//使Cookie失效
function DropCookie($key){
  global $cfg_cookie_encode,$cfg_pp_isopen,$cfg_basehost;
	if(empty($cfg_pp_isopen)
	||!ereg("\.",$cfg_basehost)||!ereg("[a-zA-Z]",$cfg_basehost))
	{
	  setcookie($key,"",mytime()-3600000,"/");
	  setcookie($key.'ckMd5',"",mytime()-3600000,"/");
	}else
	{
		 $dm = eregi_replace("http://([^\.]*)\.","",$cfg_basehost);
		 $dm = ereg_replace("/(.*)","",$dm);
	 	 setcookie($key,"",mytime(),"/",$dm);
	   setcookie($key.'ckMd5',"",mytime(),"/",$dm);
	}
}
//获得一个cookie值
function GetCookie($key){
	 global $cfg_cookie_encode;
	 if( !isset($_COOKIE[$key]) || !isset($_COOKIE[$key.'ckMd5']) ) return '';
	 else{
	   if($_COOKIE[$key.'ckMd5']!=substr(md5($cfg_cookie_encode.$_COOKIE[$key]),0,16)) return '';
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
	global $cfg_ftp_mkdir,$cfg_isSafeMode;
	if($cfg_isSafeMode||$cfg_ftp_mkdir=='Y'){ return FtpMkdir($truepath,$mmode); }
	else{
		  if(!file_exists($truepath)){
		  	 mkdir($truepath,$GLOBALS['cfg_dir_purview']);
		  	 chmod($truepath,$GLOBALS['cfg_dir_purview']);
		  	 return true;
		  }else{
		  	return true;
		  }
  }
}
//通用的更改目录或文件权限的函数
function ChmodAll($truepath,$mmode){
	global $cfg_ftp_mkdir,$cfg_isSafeMode;
	if($cfg_isSafeMode||$cfg_ftp_mkdir=='Y'){ return FtpChmod($truepath,$mmode); }
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

//会员校对密码
//把用户的密码经过此函数后与数据库的密码对比
function GetEncodePwd($pwd){
	global $cfg_pwdtype,$cfg_md5len,$cfg_ddsign;
	$cfg_pwdtype = strtolower($cfg_pwdtype);
	if($cfg_pwdtype=='md5'){
		if(empty($cfg_md5len)) $cfg_md5len = 32;
		if($cfg_md5len>=32) return md5($pwd);
		else return substr(md5($pwd),0,$cfg_md5len);
	}else if($cfg_pwdtype=='dd'){
		return DdPwdEncode($pwd,$cfg_ddsign);
	}else if($cfg_pwdtype=='md5m16'){
		return substr(md5($pwd),8,16);
	}else{
		return $pwd;
	}
}

//用户ID和密码或其它字符串安全性测试
function TestStringSafe($uid){
	if($uid!=addslashes($uid)) return false;
	if(ereg("[><\$\r\n\t '\"`\\]",$uid)) return false;
	return true;
}

//Dede密码加密算法
//加密程序
function DdPwdEncode($pwd,$sign=''){
	global $cfg_ddsign;
	if($sign=='') $sign = $cfg_ddsign;
	$rtstr = '';
	$plen = strlen($pwd);
	if($plen<10) $plenstr = '0'.$plen;
	else $plenstr = "$plen";
	$sign = substr(md5($sign),0,$plen);
	$poshandle = mt_rand(65,90);
	$rtstr .= chr($poshandle);
	$pwd = base64_encode($pwd);
	if($poshandle%2==0){
		$rtstr .= chr(ord($plenstr[0])+18);
		$rtstr .= chr(ord($plenstr[1])+36);
	}
	for($i=0;$i<strlen($pwd);$i++){
		 if($i < $plen){
		   if($poshandle%2==0) $rtstr .= $pwd[$i].$sign[$i];
		   else $rtstr .= $sign[$i].$pwd[$i];
		 }else{ $rtstr .= $pwd[$i]; }
	}
	if($poshandle%2!=0){
		$rtstr .= chr(ord($plenstr[0])+20);
		$rtstr .= chr(ord($plenstr[1])+25);
	}
	return $rtstr;
}

//解密程序
function DdPwdDecode($epwd,$sign=''){
	global $cfg_ddsign;
	$n1=0;
	$n2=0;
	$pwstr='';
	$restr='';
	if($sign=='') $sign = $cfg_ddsign;
	$rtstr = '';
	$poshandle = ord($epwd[0]);
	if($poshandle%2==0){
		$n1 = chr(ord($epwd[1])-18);
		$n2 = chr(ord($epwd[2])-36);
		$pwstr = substr($epwd,3,strlen($epwd)-3);
  }else{
  	$n1 = chr(ord($epwd[strlen($epwd)-2])-20);
		$n2 = chr(ord($epwd[strlen($epwd)-1])-25);
		$pwstr = substr($epwd,1,strlen($epwd)-3);
  }
  $pwdlen = ($n1.$n2)*2;
  $pwstrlen = strlen($pwstr);
  for($i=0;$i<$pwstrlen;$i++){
  	if($i<$pwdlen){
  	  if($poshandle%2==0){ $restr .= $pwstr[$i]; $i++; }
  	  else{ $i++; $restr .= $pwstr[$i]; }
  	}else{ $restr .= $pwstr[$i]; }
  }
  $restr = base64_decode($restr);
	return $restr;
}

/*----------------------
过滤HTML代码的函数
-----------------------*/
function htmlEncode($string) {
	$string=trim($string);
	$string=str_replace("&","&amp;",$string);
	$string=str_replace("'","&#39;",$string);
	$string=str_replace("&amp;amp;","&amp;",$string);
	$string=str_replace("&amp;quot;","&quot;",$string);
	$string=str_replace("\"","&quot;",$string);
	$string=str_replace("&amp;lt;","&lt;",$string);
	$string=str_replace("<","&lt;",$string);
	$string=str_replace("&amp;gt;","&gt;",$string);
	$string=str_replace(">","&gt;",$string);
	$string=str_replace("&amp;nbsp;","&nbsp;",$string);
	$string=nl2br($string);
	return $string;
}

function filterscript($str) {
	$str = eregi_replace("iframe","ｉｆｒａｍｅ",$str);
	$str = eregi_replace("script","ｓｃｒｉｐｔ",$str);
	return $str;
}

function AjaxHead()
{
	header("Pragma:no-cache\r\n");
  header("Cache-Control:no-cache\r\n");
  header("Expires:0\r\n");
	header("Content-Type: text/html; charset=utf-8");
}

function getarea($areaid)
{
	global $dsql;
	if($areaid==0) return '';
	if(!is_object($dsql)) $dsql = new dedesql(false);
	$areaname = $dsql->GetOne("select name from #@__area where id=$areaid");
	return $areaname['name'];
}


//用户自行扩展function

if(file_exists( dirname(__FILE__).'/inc_extend_functions.php' )){
	require_once(dirname(__FILE__).'/inc_extend_functions.php');
}

//--------------------
// 获得附加表和主表名称
//----------------------
function GetChannelTable($dsql,$id,$formtype='channel')
{
	global $cfg_dbprefix;
	$retables = array();
	$oldarrays = array(1=>'addonarticle',2=>'addonimages',3=>'addonsoft',4=>'addonflash',5=>'addonproduct',-2=>'addoninfos',-1=>'addonspec');
	if(isset($oldarrays[$id]) && $formtype!='arc')
	{
		$retables['addtable'] = $cfg_dbprefix.$oldarrays[$id];
		$retables['maintable'] = $cfg_dbprefix.'archives';
		if($id==-1) $retables['maintable'] = $cfg_dbprefix.'archivesspec';
		else if($id==-2) $retables['maintable'] = $cfg_dbprefix.'infos';
		$retables['channelid'] = $id;
	}else
	{
	   if($formtype=='arc'){
	   	$retables = $dsql->GetOne(" select c.ID as channelid,c.maintable,c.addtable from `#@__full_search` a left join  #@__channeltype c on  c.ID = a.channelid where a.aid='$id' ",MYSQL_ASSOC);
	   }
	   else{
	   	$retables = $dsql->GetOne(" Select ID as channelid,maintable,addtable From #@__channeltype where ID='$id' ",MYSQL_ASSOC);
	   }
	   if(!isset($retables['maintable'])) $retables['maintable'] = $cfg_dbprefix.'archives';
	   if(!isset($retables['addtable'])) $retables['addtable'] = '';
  }
	return $retables;
}

//-----------------------
//获取一条索引ID
//-----------------------
function GetIndexKey($dsql,$typeid=0,$channelid=0)
{
	global $typeid,$channelid,$arcrank,$title,$cfg_plus_dir;
	$typeid = (empty($typeid) ? 0 : $typeid);
	$channelid = (empty($channelid) ? 0 : $channelid);
	$arcrank = (empty($arcrank) ? 0 : $arcrank);
	$iquery = "INSERT INTO `#@__full_search` (`typeid` , `channelid` , `adminid` , `mid` , `att` , `arcrank` ,
	            `uptime` , `title` , `url` , `litpic` , `keywords` , `addinfos` , `digg` , `diggtime` )
                 VALUES ('$typeid', '$channelid', '0', '0', '0', '$arcrank',
              '0', '$title', '', '', '', '', '0', '0');
           ";
	$dsql->ExecuteNoneQuery($iquery);
	return $dsql->GetLastID();
}

//-----------------------
//更新一条整站搜索的索引记录
//-----------------------
function WriteSearchIndex($dsql,&$datas)
{
	UpSearchIndex($dsql,$datas);
}

function UpSearchIndex($dsql,&$datas)
{
	$addf = '';
	foreach($datas as $k=>$v){
		if($k!='aid') $addf .= ($addf=='' ? "`$k`='$v'" : ",`$k`='$v'");
	}
	$uquery = "update `#@__full_search` set $addf where aid = '{$datas['aid']}';";
	$rs = $dsql->ExecuteNoneQuery($uquery);
	if(!$rs){
		$gerr = $dsql->GetError();
		//$tbs = GetChannelTable($dsql,$datas['channelid'],'channel');
		//$dsql->ExecuteNoneQuery("Delete From `{$tbs['maintable']}` where ID='{$datas['aid']}'");
		//$dsql->ExecuteNoneQuery("Delete From `{$tbs['addtable']}` where aid='{$datas['aid']}'");
	  //$dsql->ExecuteNoneQuery("Delete From `#@__full_search` where aid='{$datas['aid']}'");
		echo "更新整站索引时失败，错误原因： [".$gerr."]";
		echo "<br /> SQL语句：<font color='red'>{$uquery}</font>";
		$dsql->Close();
		exit();
	}
	return $rs;
}

//
//检查某栏目下级是否包含特定频道的内容
//
function TestHasChannel($cid,$channelid,$issend=-1,$carr='')
{
	global $_Cs;
	if(!is_array($_Cs) && !is_array($carr)){ require_once(dirname(__FILE__)."/../data/cache/inc_catalog_base.php"); }
	if($channelid==0) return 1;
	if(!isset($_Cs[$cid])) return 0;
	if($issend==-1){
	  if($_Cs[$cid][1]==$channelid||$channelid==0) return 1;
	  else{
	    foreach($_Cs as $k=>$vs){
	  	  if($vs[0]==$cid) return TestHasChannel($k,$channelid,$issend,$_Cs);
	    }
	  }
	}else
	{
		if($_Cs[$cid][2]==$issend && ($_Cs[$cid][1]==$channelid||$channelid==0)) return 1;
	  else{
	    foreach($_Cs as $k=>$vs){
	  	  if($vs[0]==$cid) return TestHasChannel($k,$channelid,$issend,$_Cs);
	    }
	  }
	}
	return 0;
}

//更新栏目索引缓存
function UpDateCatCache($dsql)
{
	$cache1 = dirname(__FILE__)."/../data/cache/inc_catalog_base.php";
	$dsql->SetQuery("Select ID,reID,channeltype,issend From #@__arctype");
	$dsql->Execute();
	$fp1 = fopen($cache1,'w');
	$phph = '?';
	$fp1Header = "<{$phph}php\r\nglobal \$_Cs;\r\n\$_Cs=array();\r\n";
	fwrite($fp1,$fp1Header);
	while($row=$dsql->GetObject()){
		fwrite($fp1,"\$_Cs[{$row->ID}]=array({$row->reID},{$row->channeltype},{$row->issend});\r\n");
	}
	fwrite($fp1,"{$phph}>");
	fclose($fp1);
}

//邮件发送函数
function sendmail($email, $mailtitle, $mailbody, $headers)
{
	global $cfg_sendmail_bysmtp, $cfg_smtp_server, $cfg_smtp_port, $cfg_smtp_usermail, $cfg_smtp_user, $cfg_smtp_password, $cfg_adminemail;
	if($cfg_sendmail_bysmtp == 'Y'){
		$mailtype = 'TXT';
		require_once(dirname(__FILE__).'/mail.class.php');
		$smtp = new smtp($cfg_smtp_server,$cfg_smtp_port,true,$cfg_smtp_usermail,$cfg_smtp_password);
		$smtp->debug = false;
		$smtp->sendmail($email, $cfg_smtp_usermail, $mailtitle, $mailbody, $mailtype);
	}else{
		@mail($email, $mailtitle, $mailbody, $headers);
	}
}

$startRunMe = ExecTime();
?>