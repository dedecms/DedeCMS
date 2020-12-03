<?php
if(!defined('DEDEINC'))
{
	exit("dedecms");
}

require_once(DEDEINC.'/charset.func.php');

//拼音的缓冲数组
$pinyins = Array();
$g_ftpLink = false;

//获得当前的脚本网址
function GetCurUrl()
{
	if(!empty($_SERVER["REQUEST_URI"]))
	{
		$scriptName = $_SERVER["REQUEST_URI"];
		$nowurl = $scriptName;
	}
	else
	{
		$scriptName = $_SERVER["PHP_SELF"];
		if(empty($_SERVER["QUERY_STRING"]))
		{
			$nowurl = $scriptName;
		}
		else
		{
			$nowurl = $scriptName."?".$_SERVER["QUERY_STRING"];
		}
	}
	return $nowurl;
}

//兼容php4
if(!function_exists('file_put_contents'))
{
	function file_put_contents($n,$d)
	{
		$f=@fopen($n,"w");
		if (!$f)
		{
			return false;
		}
		else
		{
			fwrite($f,$d);
			fclose($f);
			return true;
		}
	}
}

//返回格林威治标准时间
function MyDate($format='Y-m-d H:i:s',$timest=0)
{
	global $cfg_cli_time;
	$addtime = $cfg_cli_time * 3600;
	if(empty($format))
	{
		$format = 'Y-m-d H:i:s';
	}
	return gmdate ($format,$timest+$addtime);
}

function GetAlabNum($fnum)
{
	$nums = array("０","１","２","３","４","５","６","７","８","９");
	//$fnums = "0123456789";
	$fnums = array("0","1","2","3","4","5","6","7","8","9");
	$fnum = str_replace($nums,$fnums,$fnum);
	$fnum = ereg_replace("[^0-9\.-]",'',$fnum);
	if($fnum=='')
	{
		$fnum=0;
	}
	return $fnum;
}

function Html2Text($str,$r=0)
{
	if(!function_exists('SpHtml2Text'))
	{
		require_once(DEDEINC."/inc/inc_fun_funString.php");
	}
	if($r==0)
	{
		return SpHtml2Text($str);
	}
	else
	{
		$str = SpHtml2Text(stripslashes($str));
		return addslashes($str);
	}
}

//文本转HTML
function Text2Html($txt)
{
	$txt = str_replace("  ","　",$txt);
	$txt = str_replace("<","&lt;",$txt);
	$txt = str_replace(">","&gt;",$txt);
	$txt = preg_replace("/[\r\n]{1,}/isU","<br/>\r\n",$txt);
	return $txt;
}

function AjaxHead()
{
	@header("Pragma:no-cache\r\n");
	@header("Cache-Control:no-cache\r\n");
	@header("Expires:0\r\n");
}

//中文截取2，单字节截取模式
//如果是request的内容，必须使用这个函数
function cn_substrR($str,$slen,$startdd=0)
{
	$str = cn_substr(stripslashes($str),$slen,$startdd);
	return addslashes($str);
}

//中文截取2，单字节截取模式
function cn_substr($str,$slen,$startdd=0)
{
	global $cfg_soft_lang;
	if($cfg_soft_lang=='utf-8')
	{
		return cn_substr_utf8($str,$slen,$startdd);
	}
	$restr = '';
	$c = '';
	$str_len = strlen($str);
	if($str_len < $startdd+1)
	{
		return '';
	}
	if($str_len < $startdd + $slen || $slen==0)
	{
		$slen = $str_len - $startdd;
	}
	$enddd = $startdd + $slen - 1;
	for($i=0;$i<$str_len;$i++)
	{
		if($startdd==0)
		{
			$restr .= $c;
		}
		else if($i > $startdd)
		{
			$restr .= $c;
		}

		if(ord($str[$i])>0x80)
		{
			if($str_len>$i+1)
			{
				$c = $str[$i].$str[$i+1];
			}
			$i++;
		}
		else
		{
			$c = $str[$i];
		}

		if($i >= $enddd)
		{
			if(strlen($restr)+strlen($c)>$slen)
			{
				break;
			}
			else
			{
				$restr .= $c;
				break;
			}
		}
	}
	return $restr;
}

//utf-8中文截取，单字节截取模式
function cn_substr_utf8($str, $length, $start=0)
{
	if(strlen($str) < $start+1)
	{
		return '';
	}
	preg_match_all("/./su", $str, $ar);
	$str = '';
	$tstr = '';

	//为了兼容mysql4.1以下版本,与数据库varchar一致,这里使用按字节截取
	for($i=0; isset($ar[0][$i]); $i++)
	{
		if(strlen($tstr) < $start)
		{
			$tstr .= $ar[0][$i];
		}
		else
		{
			if(strlen($str) < $length + strlen($ar[0][$i]) )
			{
				$str .= $ar[0][$i];
			}
			else
			{
				break;
			}
		}
	}
	return $str;
}

function GetMkTime($dtime)
{
	global $cfg_cli_time;
	if(!ereg("[^0-9]",$dtime))
	{
		return $dtime;
	}
	$dtime = trim($dtime);
	$dt = Array(1970,1,1,0,0,0);
	$dtime = ereg_replace("[\r\n\t]|日|秒"," ",$dtime);
	$dtime = str_replace("年","-",$dtime);
	$dtime = str_replace("月","-",$dtime);
	$dtime = str_replace("时",":",$dtime);
	$dtime = str_replace("分",":",$dtime);
	$dtime = trim(ereg_replace("[ ]{1,}"," ",$dtime));
	$ds = explode(" ",$dtime);
	$ymd = explode("-",$ds[0]);
	if(!isset($ymd[1]))
	{
		$ymd = explode(".",$ds[0]);
	}
	if(isset($ymd[0]))
	{
		$dt[0] = $ymd[0];
	}
	if(isset($ymd[1]))
	{
		$dt[1] = $ymd[1];
	}
	if(isset($ymd[2]))
	{
		$dt[2] = $ymd[2];
	}
	if(strlen($dt[0])==2)
	{
		$dt[0] = '20'.$dt[0];
	}
	if(isset($ds[1]))
	{
		$hms = explode(":",$ds[1]);
		if(isset($hms[0]))
		{
			$dt[3] = $hms[0];
		}
		if(isset($hms[1]))
		{
			$dt[4] = $hms[1];
		}
		if(isset($hms[2]))
		{
			$dt[5] = $hms[2];
		}
	}
	foreach($dt as $k=>$v)
	{
		$v = ereg_replace("^0{1,}",'',trim($v));
		if($v=='')
		{
			$dt[$k] = 0;
		}
	}
	$mt = @gmmktime($dt[3],$dt[4],$dt[5],$dt[1],$dt[2],$dt[0]) - 3600 * $cfg_cli_time;
	if(!empty($mt))
	{
		return $mt;
	}
	else
	{
		return time();
	}
}

function SubDay($ntime,$ctime)
{
	$dayst = 3600 * 24;
	$cday = ceil(($ntime-$ctime)/$dayst);
	return $cday;
}

function AddDay($ntime,$aday)
{
	$dayst = 3600 * 24;
	$oktime = $ntime + ($aday * $dayst);
	return $oktime;
}

function GetDateTimeMk($mktime)
{
	return MyDate('Y-m-d H:i:s',$mktime);
}

function GetDateMk($mktime)
{
	return MyDate("Y-m-d",$mktime);
}

function GetIP()
{
	if(!empty($_SERVER["HTTP_CLIENT_IP"]))
	{
		$cip = $_SERVER["HTTP_CLIENT_IP"];
	}
	else if(!empty($_SERVER["HTTP_X_FORWARDED_FOR"]))
	{
		$cip = $_SERVER["HTTP_X_FORWARDED_FOR"];
	}
	else if(!empty($_SERVER["REMOTE_ADDR"]))
	{
		$cip = $_SERVER["REMOTE_ADDR"];
	}
	else
	{
		$cip = '';
	}
	preg_match("/[\d\.]{7,15}/", $cip, $cips);
	$cip = isset($cips[0]) ? $cips[0] : 'unknown';
	unset($cips);
	return $cip;
}

//获取拼音以gbk编码为准
function GetPinyin($str,$ishead=0,$isclose=1)
{
	global $cfg_soft_lang;
	if(!function_exists('SpGetPinyin'))
	{
		require_once(DEDEINC."/inc/inc_fun_funAdmin.php");
	}
	if($cfg_soft_lang=='utf-8')
	{
		return SpGetPinyin(utf82gb($str),$ishead,$isclose);
	}
	else
	{
		return SpGetPinyin($str,$ishead,$isclose);
	}
}

function GetNewInfo()
{
	if(!function_exists('SpGetNewInfo'))
	{
		require_once(DEDEINC."/inc/inc_fun_funAdmin.php");
	}
	return SpGetNewInfo();
}

function UpdateStat()
{
	include_once(DEDEINC."/inc/inc_stat.php");
	return SpUpdateStat();
}

$arrs1 = array(0x63,0x66,0x67,0x5f,0x70,0x6f,0x77,0x65,0x72,0x62,0x79);
$arrs2 = array(0x20,0x3c,0x61,0x20,0x68,0x72,0x65,0x66,0x3d,0x68,0x74,0x74,0x70,0x3a,0x2f,0x2f,
0x77,0x77,0x77,0x2e,0x64,0x65,0x64,0x65,0x63,0x6d,0x73,0x2e,0x63,0x6f,0x6d,0x20,0x74,0x61,0x72,
0x67,0x65,0x74,0x3d,0x27,0x5f,0x62,0x6c,0x61,0x6e,0x6b,0x27,0x3e,0x50,0x6f,0x77,0x65,0x72,0x20,
0x62,0x79,0x20,0x44,0x65,0x64,0x65,0x43,0x6d,0x73,0x3c,0x2f,0x61,0x3e);

function ShowMsg($msg,$gourl,$onlymsg=0,$limittime=0)
{
	if(empty($GLOBALS['cfg_phpurl']))
	{
		$GLOBALS['cfg_phpurl'] = '..';
	}
	$htmlhead  = "<html>\r\n<head>\r\n<title>DEDECMS提示信息</title>\r\n<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />\r\n";
	$htmlhead .= "<base target='_self'/>\r\n<style>div{line-height:160%;}</style></head>\r\n<body leftmargin='0' topmargin='0'>\r\n<center>\r\n<script>\r\n";
	$htmlfoot  = "</script>\r\n</center>\r\n</body>\r\n</html>\r\n";

	if($limittime==0)
	{
		$litime = 1000;
	}
	else
	{
		$litime = $limittime;
	}

	if($gourl=="-1")
	{
		if($limittime==0)
		{
			$litime = 5000;
		}
		$gourl = "javascript:history.go(-1);";
	}

	if($gourl==''||$onlymsg==1)
	{
		$msg = "<script>alert(\"".str_replace("\"","“",$msg)."\");</script>";
	}
	else
	{
		$func = "      var pgo=0;
      function JumpUrl(){
        if(pgo==0){ location='$gourl'; pgo=1; }
      }\r\n";
		$rmsg = $func;
		$rmsg .= "document.write(\"<br /><div style='width:450px;padding:0px;border:1px solid #D1DDAA;'>";
		$rmsg .= "<div style='padding:6px;font-size:12px;border-bottom:1px solid #D1DDAA;background:#DBEEBD url({$GLOBALS['cfg_phpurl']}/img/wbg.gif)';'><b>DEDECMS 提示信息！</b></div>\");\r\n";
		$rmsg .= "document.write(\"<div style='height:130px;font-size:10pt;background:#ffffff'><br />\");\r\n";
		$rmsg .= "document.write(\"".str_replace("\"","“",$msg)."\");\r\n";
		$rmsg .= "document.write(\"";
		if($onlymsg==0)
		{
			if($gourl!="javascript:;" && $gourl!="")
			{
				$rmsg .= "<br /><a href='{$gourl}'>如果你的浏览器没反应，请点击这里...</a>";
			}
			$rmsg .= "<br/></div>\");\r\n";
			if($gourl!="javascript:;" && $gourl!='')
			{
				$rmsg .= "setTimeout('JumpUrl()',$litime);";
			}
		}
		else
		{
			$rmsg .= "<br/><br/></div>\");\r\n";
		}
		$msg  = $htmlhead.$rmsg.$htmlfoot;
	}
	echo $msg;
}

function ExecTime()
{
	$time = explode(" ", microtime());
	$usec = (double)$time[0];
	$sec = (double)$time[1];
	return $sec + $usec;
}

function GetEditor($fname,$fvalue,$nheight="350",$etype="Basic",$gtype="print",$isfullpage="false")
{
	if(!function_exists('SpGetEditor'))
	{
		require_once(DEDEINC."/inc/inc_fun_funAdmin.php");
	}
	return SpGetEditor($fname,$fvalue,$nheight,$etype,$gtype,$isfullpage);
}

function GetTemplets($filename)
{
	if(file_exists($filename))
	{
		$fp = fopen($filename,"r");
		$rstr = fread($fp,filesize($filename));
		fclose($fp);
		return $rstr;
	}
	else
	{
		return '';
	}
}

function GetSysTemplets($filename)
{
	return GetTemplets($GLOBALS['cfg_basedir'].$GLOBALS['cfg_templets_dir'].'/system/'.$filename);
}

function AttDef($oldvar,$nv)
{
	return empty($oldvar) ? $nv : $oldvar;
}

function dd2char($ddnum)
{
	$ddnum = strval($ddnum);
	$slen = strlen($ddnum);
	$okdd = '';
	$nn = '';
	for($i=0;$i<$slen;$i++)
	{
		if(isset($ddnum[$i+1]))
		{
			$n = $ddnum[$i].$ddnum[$i+1];
			if( ($n>96 && $n<123) || ($n>64 && $n<91) )
			{
				$okdd .= chr($n);
				$i++;
			}
			else
			{
				$okdd .= $ddnum[$i];
			}
		}
		else
		{
			$okdd .= $ddnum[$i];
		}
	}
	return $okdd;
}

function PutCookie($key,$value,$kptime=0,$pa="/")
{
	global $cfg_cookie_encode;
	setcookie($key,$value,time()+$kptime,$pa);
	setcookie($key.'__ckMd5',substr(md5($cfg_cookie_encode.$value),0,16),time()+$kptime,$pa);
}

function DropCookie($key)
{
	setcookie($key,'',time()-360000,"/");
	setcookie($key.'__ckMd5','',time()-360000,"/");
}

function GetCookie($key)
{
	global $cfg_cookie_encode;
	if( !isset($_COOKIE[$key]) || !isset($_COOKIE[$key.'__ckMd5']) )
	{
		return '';
	}
	else
	{
		if($_COOKIE[$key.'__ckMd5']!=substr(md5($cfg_cookie_encode.$_COOKIE[$key]),0,16))
		{
			return '';
		}
		else
		{
			return $_COOKIE[$key];
		}
	}
}

function GetCkVdValue()
{
	@session_start();
	return isset($_SESSION['dd_ckstr']) ? $_SESSION['dd_ckstr'] : '';
}

//php某些版本有Bug，不能在同一作用域中同时读session并改注销它，因此调用后需执行本函数
function ResetVdValue()
{
	@session_start();
	$_SESSION['dd_ckstr'] = '';
	$_SESSION['dd_ckstr_last'] = '';
}

function FtpMkdir($truepath,$mmode,$isMkdir=true)
{
	global $cfg_basedir,$cfg_ftp_root,$g_ftpLink;
	OpenFtp();
	$ftproot = ereg_replace($cfg_ftp_root.'$','',$cfg_basedir);
	$mdir = ereg_replace('^'.$ftproot,'',$truepath);
	if($isMkdir)
	{
		ftp_mkdir($g_ftpLink,$mdir);
	}
	return ftp_site($g_ftpLink,"chmod $mmode $mdir");
}

function FtpChmod($truepath,$mmode)
{
	return FtpMkdir($truepath,$mmode,false);
}

function OpenFtp()
{
	global $cfg_basedir,$cfg_ftp_host,$cfg_ftp_port, $cfg_ftp_user,$cfg_ftp_pwd,$cfg_ftp_root,$g_ftpLink;
	if(!$g_ftpLink)
	{
		if($cfg_ftp_host=='')
		{
			echo "由于你的站点的PHP配置存在限制，程序尝试用FTP进行目录操作，你必须在后台指定FTP相关的变量！";
			exit();
		}
		$g_ftpLink = ftp_connect($cfg_ftp_host,$cfg_ftp_port);
		if(!$g_ftpLink)
		{
			echo "连接FTP失败！";
			exit();
		}
		if(!ftp_login($g_ftpLink,$cfg_ftp_user,$cfg_ftp_pwd))
		{
			echo "登陆FTP失败！";
			exit();
		}
	}
}

function CloseFtp()
{
	global $g_ftpLink;
	if($g_ftpLink)
	{
		@ftp_quit($g_ftpLink);
	}
}

function MkdirAll($truepath,$mmode)
{
	global $cfg_ftp_mkdir,$isSafeMode,$cfg_dir_purview;
	if($isSafeMode||$cfg_ftp_mkdir=='Y')
	{
		return FtpMkdir($truepath,$mmode);
	}
	else
	{
		if(!file_exists($truepath))
		{
			mkdir($truepath,$cfg_dir_purview);
			chmod($truepath,$cfg_dir_purview);
			return true;
		}
		else
		{
			return true;
		}
	}
}

function ParCv($n)
{
	return chr($n);
}

function ChmodAll($truepath,$mmode)
{
	global $cfg_ftp_mkdir,$isSafeMode;
	if($isSafeMode||$cfg_ftp_mkdir=='Y')
	{
		return FtpChmod($truepath,$mmode);
	}
	else
	{
		return chmod($truepath,'0'.$mmode);
	}
}

function CreateDir($spath)
{
	if(!function_exists('SpCreateDir'))
	{
		require_once(DEDEINC.'/inc/inc_fun_funAdmin.php');
	}
	return SpCreateDir($spath);
}

// $rptype = 0 表示仅替换 html标记
// $rptype = 1 表示替换 html标记同时去除连续空白字符
// $rptype = 2 表示替换 html标记同时去除所有空白字符
// $rptype = -1 表示仅替换 html危险的标记
function HtmlReplace($str,$rptype=0)
{
	$str = stripslashes($str);
	if($rptype==0)
	{
		$str = htmlspecialchars($str);
	}
	else if($rptype==1)
	{
		$str = htmlspecialchars($str);
		$str = str_replace("　",' ',$str);
		$str = ereg_replace("[\r\n\t ]{1,}",' ',$str);
	}
	else if($rptype==2)
	{
		$str = htmlspecialchars($str);
		$str = str_replace("　",'',$str);
		$str = ereg_replace("[\r\n\t ]",'',$str);
	}
	else
	{
		$str = ereg_replace("[\r\n\t ]{1,}",' ',$str);
		$str = eregi_replace('script','ｓｃｒｉｐｔ',$str);
		$str = eregi_replace("<[/]{0,1}(link|meta|ifr|fra)[^>]*>",'',$str);
	}
	return addslashes($str);
}

//获得某文档的所有tag
function GetTags($aid)
{
	global $dsql;
	$tags = '';
	$query = "Select tag From `#@__taglist` where aid='$aid' ";
	$dsql->Execute('tag',$query);
	while($row = $dsql->GetArray('tag'))
	{
		$tags .= ($tags=='' ? $row['tag'] : ','.$row['tag']);
	}
	return $tags;
}

function ParamError()
{
	ShowMsg('对不起，你输入的参数有误！','javascript:;');
	exit();
}

//过滤用于搜索的字符串
function FilterSearch($keyword)
{
	global $cfg_soft_lang;
	if($cfg_soft_lang=='utf-8')
	{
		$keyword = ereg_replace("[ \"\r\n\t\$\\><']",'',$keyword);
		if($keyword != stripslashes($keyword))
		{
			return '';
		}
		else
		{
			return $keyword;
		}
	}
	else
	{
		$restr = '';
		for($i=0;isset($keyword[$i]);$i++)
		{
			if(ord($keyword[$i]) > 0x80)
			{
				if(isset($keyword[$i+1]) && ord($keyword[$i+1]) > 0x40)
				{
					$restr .= $keyword[$i].$keyword[$i+1];
					$i++;
				}
				else
				{
					$restr .= ' ';
				}
			}
			else
			{
				if(eregi("[^0-9a-z@#\.]",$keyword[$i]))
				{
					$restr .= ' ';
				}
				else
				{
					$restr .= $keyword[$i];
				}
			}
		}
	}
	return $restr;
}

//处理禁用HTML但允许换行的内容
function TrimMsg($msg)
{
	$msg = trim(stripslashes($msg));
	$msg = nl2br(htmlspecialchars($msg));
	$msg = str_replace("  ","&nbsp;&nbsp;",$msg);
	return addslashes($msg);
}

//获取单篇文档信息
function GetOneArchive($aid)
{
	global $dsql;
	include_once(DEDEINC."/channelunit.func.php");
	$aid = trim(ereg_replace('[^0-9]','',$aid));
	$reArr = array();

	$chRow = $dsql->GetOne("Select arc.*,ch.maintable,ch.addtable,ch.issystem From `#@__arctiny` arc left join `#@__channeltype` ch on ch.id=arc.channel where arc.id='$aid' ");

	if(!is_array($chRow)) {
		return $reArr;
	}
	else {
		if(empty($chRow['maintable'])) $chRow['maintable'] = '#@__archives';
	}

	if($chRow['issystem']!=-1)
	{
		$nquery = " Select arc.*,tp.typedir,tp.topid,tp.namerule,tp.moresite,tp.siteurl,tp.sitepath
		            From `{$chRow['maintable']}` arc left join `#@__arctype` tp on tp.id=arc.typeid
		            where arc.id='$aid' ";
	}
	else
	{
		$nquery = " Select arc.*,1 as ismake,0 as money,'' as filename,tp.typedir,tp.topid,tp.namerule,tp.moresite,tp.siteurl,tp.sitepath
		            From `{$chRow['addtable']}` arc left join `#@__arctype` tp on tp.id=arc.typeid
		            where arc.aid='$aid' ";
	}

	$arcRow = $dsql->GetOne($nquery);

	if(!is_array($arcRow)) {
		return $reArr;
	}

	if(!isset($arcRow['description'])) {
		$arcRow['description'] = '';
	}

	if(empty($arcRow['description']) && isset($arcRow['body'])) {
		$arcRow['description'] = cn_substr(html2text($arcRow['body']),250);
	}

	if(!isset($arcRow['pubdate'])) {
		$arcRow['pubdate'] = $arcRow['senddate'];
	}

	if(!isset($arcRow['notpost'])) {
		$arcRow['notpost'] = 0;
	}

	$reArr = $arcRow;
	$reArr['aid']    = $aid;
	$reArr['topid']  = $arcRow['topid'];
	$reArr['arctitle'] = $arcRow['title'];
	$reArr['arcurl'] = GetFileUrl($aid,$arcRow['typeid'],$arcRow['senddate'],$reArr['title'],$arcRow['ismake'],$arcRow['arcrank'],$arcRow['namerule'],
	$arcRow['typedir'],$arcRow['money'],$arcRow['filename'],$arcRow['moresite'],$arcRow['siteurl'],$arcRow['sitepath']);
	return $reArr;

}

//获取模型的表信息
function GetChannelTable($id,$formtype='channel')
{
	global $dsql;
	if($formtype == 'archive')
	{
		$query = "select ch.maintable, ch.addtable from #@__arctiny tin left join #@__channeltype ch on ch.id=tin.channel where tin.id='$id'";
	}
	elseif($formtype == 'typeid')
	{
		$query = "select ch.maintable, ch.addtable from #@__arctype act left join #@__channeltype ch on ch.id=act.channeltype where act.id='$id'";
	}
	else
	{
		$query = "select maintable, addtable from #@__channeltype where id='$id'";
	}
	$row = $dsql->getone($query);
	return $row;
}

function jstrim($str,$len)
{
	$str = preg_replace("/{quote}(.*){\/quote}/is",'',$str);
	$str = str_replace('&lt;br/&gt;',' ',$str);
	$str = cn_substr($str,$len);
	$str = ereg_replace("['\"\r\n]","",$str);
	return $str;
}


//自定义函数接口
if( file_exists(DEDEINC.'/extend.func.php') ) {
	require_once(DEDEINC.'/extend.func.php');
}

?>