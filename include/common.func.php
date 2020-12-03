<?php
if(!defined('DEDEINC')) exit('dedecms');

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

//Remove the exploer'bug XSS
function RemoveXSS($val) {
   // remove all non-printable characters. CR(0a) and LF(0b) and TAB(9) are allowed
   // this prevents some character re-spacing such as <java\0script>
   // note that you have to handle splits with \n, \r, and \t later since they *are* allowed in some inputs
   $val = preg_replace('/([\x00-\x08,\x0b-\x0c,\x0e-\x19])/', '', $val);
   // straight replacements, the user should never need these since they're normal characters
   // this prevents like <IMG SRC=@avascript:alert('XSS')>
   $search = 'abcdefghijklmnopqrstuvwxyz';
   $search .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
   $search .= '1234567890!@#$%^&*()';
   $search .= '~`";:?+/={}[]-_|\'\\';
   for ($i = 0; $i < strlen($search); $i++) {
      // ;? matches the ;, which is optional
      // 0{0,7} matches any padded zeros, which are optional and go up to 8 chars

      // @ @ search for the hex values
      $val = preg_replace('/(&#[xX]0{0,8}'.dechex(ord($search[$i])).';?)/i', $search[$i], $val); // with a ;
      // @ @ 0{0,7} matches '0' zero to seven times
      $val = preg_replace('/(&#0{0,8}'.ord($search[$i]).';?)/', $search[$i], $val); // with a ;
   }

   // now the only remaining whitespace attacks are \t, \n, and \r
   $ra1 = array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', 'style', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound', 'title', 'base');
   $ra2 = array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');
   $ra = array_merge($ra1, $ra2);

   $found = true; // keep replacing as long as the previous round replaced something
   while ($found == true) {
      $val_before = $val;
      for ($i = 0; $i < sizeof($ra); $i++) {
         $pattern = '/';
         for ($j = 0; $j < strlen($ra[$i]); $j++) {
            if ($j > 0) {
               $pattern .= '(';
               $pattern .= '(&#[xX]0{0,8}([9ab]);)';
               $pattern .= '|';
               $pattern .= '|(&#0{0,8}([9|10|13]);)';
               $pattern .= ')*';
            }
            $pattern .= $ra[$i][$j];
         }
         $pattern .= '/i';
         $replacement = substr($ra[$i], 0, 2).'<x>'.substr($ra[$i], 2); // add in <> to nerf the tag
         $val = preg_replace($pattern, $replacement, $val); // filter out the hex tags
         if ($val_before == $val) {
            // no replacements were made, so exit the loop
            $found = false;
         }
      }
   }
   return $val;
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
	if($mktime=="0") return "暂无";
	else return MyDate("Y-m-d",$mktime);
}

function FloorTime($seconds)
{
	$times = '';
	$days = floor(($seconds/86400)%30);
	$hours = floor(($seconds/3600)%24);
	$minutes = floor(($seconds/60)%60);
	$seconds = floor($seconds%60);
	if($seconds >= 1) $times .= $seconds.'秒';
	if($minutes >= 1) $times = $minutes.'分钟 '.$times;
	if($hours >= 1) $times = $hours.'小时 '.$times;
	if($days >= 1)  $times = $days.'天';
	if($days > 30) return false;
	$times .= '前';
	return str_replace(" ", '', $times);
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
	if(empty($GLOBALS['cfg_phpurl'])) $GLOBALS['cfg_phpurl'] = '..';

	$htmlhead  = "<html>\r\n<head>\r\n<title>DEDECMS提示信息</title>\r\n<meta http-equiv=\"Content-Type\" content=\"text/html; charset=gb2312\" />\r\n";
	$htmlhead .= "<base target='_self'/>\r\n<style>div{line-height:160%;}</style></head>\r\n<body leftmargin='0' topmargin='0'>".(isset($GLOBALS['ucsynlogin']) ? $GLOBALS['ucsynlogin'] : '')."\r\n<center>\r\n<script>\r\n";
	$htmlfoot  = "</script>\r\n</center>\r\n</body>\r\n</html>\r\n";

	$litime = ($limittime==0 ? 1000 : $limittime);
	$func = '';

	if($gourl=='-1')
	{
		if($limittime==0) $litime = 5000;
		$gourl = "javascript:history.go(-1);";
	}

	if($gourl=='' || $onlymsg==1)
	{
		$msg = "<script>alert(\"".str_replace("\"","“",$msg)."\");</script>";
	}
	else
	{
		//当网址为:close::objname 时, 关闭父框架的id=objname元素
		if(eregi('close::',$gourl))
		{
			$tgobj = trim(eregi_replace('close::', '', $gourl));
			$gourl = 'javascript:;';
			$func .= "window.parent.document.getElementById('{$tgobj}').style.display='none';\r\n";
		}
		
		$func .= "      var pgo=0;
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
			if( $gourl != 'javascript:;' && $gourl != '')
			{
				$rmsg .= "<br /><a href='{$gourl}'>如果你的浏览器没反应，请点击这里...</a>";
				$rmsg .= "<br/></div>\");\r\n";
				$rmsg .= "setTimeout('JumpUrl()',$litime);";
			}
			else
			{
				$rmsg .= "<br/></div>\");\r\n";
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
	return isset($_SESSION['securimage_code_value']) ? $_SESSION['securimage_code_value'] : '';
}

//php某些版本有Bug，不能在同一作用域中同时读session并改注销它，因此调用后需执行本函数
function ResetVdValue()
{
	@session_start();
	$_SESSION['securimage_code_value'] = '';
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
		$keyword = ereg_replace("[\"\r\n\t\$\\><']",'',$keyword);
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

function jstrimjajx($str,$len)
{
	$str = preg_replace("/{quote}(.*){\/quote}/is",'',$str);
	$str = str_replace('&lt;br/&gt;',' ',$str);
	$str = cn_substr($str,$len);
	$str = ereg_replace("['\"\r\n]","",$str);
	$str = str_replace('&lt;', '<', $str);
	$str = str_replace('&gt;', '>', $str);
	return $str;
}

/*-------------------------------
//管理员上传文件的通用函数
//filetype: image、media、addon
//return: -1 没选定上传文件，0 文件类型不允许, -2 保存失败，其它：返回上传后的文件名
//$file_type='' 对于swfupload上传的文件， 因为没有filetype，所以需指定，并且有些特殊之处不同
-------------------------------*/
function AdminUpload($uploadname, $ftype='image', $rnddd=0, $watermark=true, $filetype='' )
{
	global $dsql, $cuserLogin, $cfg_addon_savetype, $cfg_dir_purview;
	global $cfg_basedir, $cfg_image_dir, $cfg_soft_dir, $cfg_other_medias;
	global $cfg_imgtype, $cfg_softtype, $cfg_mediatype;
	if($watermark) include_once(DEDEINC.'/image.func.php');
	
	$file_tmp = isset($GLOBALS[$uploadname]) ? $GLOBALS[$uploadname] : '';
	if($file_tmp=='' || !is_uploaded_file($file_tmp) )
	{
		return -1;
	}
	
	$file_tmp = $GLOBALS[$uploadname];
	$file_size = filesize($file_tmp);
	$file_type = $filetype=='' ? strtolower(trim($GLOBALS[$uploadname.'_type'])) : $filetype;
	
	$file_name = isset($GLOBALS[$uploadname.'_name']) ? $GLOBALS[$uploadname.'_name'] : '';
	$file_snames = explode('.', $file_name);
	$file_sname = strtolower(trim($file_snames[count($file_snames)-1]));
	
	if($ftype=='image' || $ftype=='imagelit')
	{
		$filetype = '1';
		$sparr = Array('image/pjpeg', 'image/jpeg', 'image/gif', 'image/png', 'image/xpng', 'image/wbmp');
		if(!in_array($file_type, $sparr)) return 0;
		if($file_sname=='')
		{
			if($file_type=='image/gif') $file_sname = 'jpg';
			else if($file_type=='image/png' || $file_type=='image/xpng') $file_sname = 'png';
			else if($file_type=='image/wbmp') $file_sname = 'bmp';
			else $file_sname = 'jpg';
		}
		$filedir = $cfg_image_dir.'/'.MyDate($cfg_addon_savetype, time());
	}
	else if($ftype=='media')
	{
		$filetype = '3';
		if( !eregi($cfg_mediatype, $file_sname) ) return 0;
		$filedir = $cfg_other_medias.'/'.MyDate($cfg_addon_savetype, time());
	}
	else
	{
		$filetype = '4';
		$cfg_softtype .= '|'.$cfg_mediatype.'|'.$cfg_imgtype;
		$cfg_softtype = ereg_replace('||', '|', $cfg_softtype);
		if( !eregi($cfg_softtype, $file_sname) ) return 0;
		$filedir = $cfg_soft_dir.'/'.MyDate($cfg_addon_savetype, time());
	}
	if(!is_dir(DEDEROOT.$filedir))
	{
		MkdirAll($cfg_basedir.$filedir, $cfg_dir_purview);
		CloseFtp();
	}
	$filename = $cuserLogin->getUserID().'-'.dd2char(MyDate('ymdHis', time())).$rnddd;
	if($ftype=='imagelit') $filename .= '-L';
	if( file_exists($cfg_basedir.$filedir.'/'.$filename.'.'.$file_sname) )
	{
		for($i=50; $i <= 5000; $i++)
		{
			if( !file_exists($cfg_basedir.$filedir.'/'.$filename.'-'.$i.'.'.$file_sname) )
			{
				$filename = $filename.'-'.$i;
				break;
			}
		}
	}
	$fileurl = $filedir.'/'.$filename.'.'.$file_sname;
	$rs = move_uploaded_file($file_tmp, $cfg_basedir.$fileurl);
	if(!$rs) return -2;
	if($ftype=='image' && $watermark)
	{
		WaterImg($cfg_basedir.$fileurl, 'up');
	}
	
	//保存信息到数据库
	$title = $filename.'.'.$file_sname;
	$inquery = "INSERT INTO `#@__uploads`(title,url,mediatype,width,height,playtime,filesize,uptime,mid)
        VALUES ('$title','$fileurl','$filetype','0','0','0','".filesize($cfg_basedir.$fileurl)."','".time()."','".$cuserLogin->getUserID()."'); ";
	$dsql->ExecuteNoneQuery($inquery);
	$fid = $dsql->GetLastID();
	AddMyAddon($fid, $fileurl);
	return $fileurl;
}

//邮箱格式检查
function CheckEmail($email)
{
	return eregi("^[0-9a-z][a-z0-9\._-]{1,}@[a-z0-9-]{1,}[a-z0-9]\.[a-z\.]{1,}[a-z]$", $email);
}

//前台会员通用上传函数
//$upname 是文件上传框的表单名，而不是表单的变量
//$handname 允许用户手工指定网址情况下的网址
function MemberUploads($upname,$handname,$userid=0,$utype='image',$exname='',$maxwidth=0,$maxheight=0,$water=false,$isadmin=false)
{
	global $cfg_imgtype,$cfg_mb_addontype,$cfg_mediatype,$cfg_user_dir,$cfg_basedir,$cfg_dir_purview;
	
	//当为游客投稿的情况下，这个 id 为 0
	if( empty($userid) ) $userid = 0;
	if(!is_dir($cfg_basedir.$cfg_user_dir."/$userid"))
	{
			MkdirAll($cfg_basedir.$cfg_user_dir."/$userid", $cfg_dir_purview);
			CloseFtp();
	}
	//有上传文件
	$allAllowType = str_replace('||', '|', $cfg_imgtype.'|'.$cfg_mediatype.'|'.$cfg_mb_addontype);
	if(!empty($GLOBALS[$upname]) && is_uploaded_file($GLOBALS[$upname]))
	{
		$nowtme = time();
		$GLOBALS[$upname.'_name'] = trim(ereg_replace("[ \r\n\t\*\%\\/\?><\|\":]{1,}",'',$GLOBALS[$upname.'_name']));
		//源文件类型检查
		if($utype=='image')
		{
			if(!eregi("\.(".$cfg_imgtype.")$", $GLOBALS[$upname.'_name']))
			{
				ShowMsg("你所上传的图片类型不在许可列表，请上传{$cfg_imgtype}类型！",'-1');
				exit();
			}
			$sparr = Array("image/pjpeg","image/jpeg","image/gif","image/png","image/xpng","image/wbmp");
			$imgfile_type = strtolower(trim($GLOBALS[$upname.'_type']));
			if(!in_array($imgfile_type,$sparr))
			{
				ShowMsg('上传的图片格式错误，请使用JPEG、GIF、PNG、WBMP格式的其中一种！', '-1');
				exit();
			}
		}
		else if($utype=='flash' && !eregi("\.swf$", $GLOBALS[$upname.'_name']))
		{
			ShowMsg('上传的文件必须为flash文件！', '-1');
			exit();
		}
		else if($utype=='media' && !eregi("\.(".$cfg_mediatype.")$",$GLOBALS[$upname.'_name']))
		{
			ShowMsg('你所上传的文件类型必须为：'.$cfg_mediatype, '-1');
			exit();
		}
		else if(!eregi("\.(".$allAllowType.")$", $GLOBALS[$upname.'_name']))
		{
			ShowMsg("你所上传的文件类型不被允许！",'-1');
			exit();
		}
		//再次严格检测文件扩展名是否符合系统定义的类型
		$fs = explode('.', $GLOBALS[$upname.'_name']);
		$sname = $fs[count($fs)-1];
		$alltypes = explode('|', $allAllowType);
		if(!in_array(strtolower($sname), $alltypes))
		{
			ShowMsg('你所上传的文件类型不被允许！', '-1');
			exit();
		}
		//强制禁止的文件类型
		if(eregi("\.(asp|php|pl|cgi|shtm|js)", $sname))
		{
			ShowMsg('你上传的文件为系统禁止的类型！', '-1');
			exit();
		}
		if($exname=='')
		{
			$filename = $cfg_user_dir."/$userid/".dd2char($nowtme.'-'.mt_rand(1000,9999)).'.'.$sname;
		}
		else
		{
			$filename = $cfg_user_dir."/{$userid}/{$exname}.".$sname;
		}
		move_uploaded_file($GLOBALS[$upname], $cfg_basedir.$filename) or die("上传文件到 {$filename} 失败！");
		@unlink($GLOBALS[$upname]);
		
		if(@filesize($cfg_basedir.$filename) > $GLOBALS['cfg_mb_upload_size'] * 1024)
		{
			@unlink($cfg_basedir.$filename);
			ShowMsg('你上传的文件超出系统大小限制！', '-1');
			exit();
		}
		
		//加水印或缩小图片
		if($utype=='image')
		{
			include_once(DEDEINC.'/image.func.php');
			if($maxwidth>0 || $maxheight>0)
			{
				ImageResize($cfg_basedir.$filename, $maxwidth, $maxheight);
			}
			else if($water)
			{
				WaterImg($cfg_basedir.$filename);
			}
		}
		return $filename;
	}
	//没有上传文件
	else
	{
		//强制禁止的文件类型
		if($handname=='')
		{
			return $handname;
		}
		else if(eregi("\.(asp|php|pl|cgi|shtm|js)", $handname))
		{
			exit('Not allow filename for not safe!');
		}
		else if( !eregi("\.(".$allAllowType.")$", $handname) )
		{
			exit('Not allow filename for filetype!');
		}
		else if( !eregi('^http:', $handname) && !eregi('^'.$cfg_user_dir.'/'.$userid, $handname) && !$isadmin )
		{
			exit('Not allow filename for not userdir!');
		}
		return $handname;
	}
}


//自定义函数接口
if( file_exists(DEDEINC.'/extend.func.php') )
{
	require_once(DEDEINC.'/extend.func.php');
}

?>