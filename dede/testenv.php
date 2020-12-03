<?php
@set_time_limit(0);
/**
 * 系统运行环境检测
 *
 * @version        $Id: testenv.php 13:57 2011/11/10 tianya $
 * @package        DedeCMS.Administrator
 * @copyright      Copyright (c) 2007 - 2010, DesDev, Inc.
 * @license        http://help.dedecms.com/usersguide/license.html
 * @link           http://www.dedecms.com
 */
require_once(dirname(__FILE__)."/config.php");
CheckPurview('sys_Edit');
$action = isset($action)? $action : '';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $GLOBALS['cfg_soft_lang']; ?>">
<title>系统运行目录权限检测</title>
<link rel="stylesheet" type="text/css" href="css/base.css" />
<link rel="stylesheet" type="text/css" href="css/indexbody.css" />
<script type="text/javascript" src="../include/js/jquery/jquery.js" ></script>
</head>
<body leftmargin="8" topmargin='8' bgcolor="#FFFFFF" style="min-width:840px">
<?php
if(!function_exists('TestWriteable'))
{
	// 检测是否可写
	function TestWriteable($d, $c=TRUE)
	{
		$tfile = '_write_able.txt';
		$d = preg_replace("/\/$/", '', $d);
		$fp = @fopen($d.'/'.$tfile,'w');
		if(!$fp)
		{
			if( $c==false )
			{
				@chmod($d, 0777);
				return false;
			}
			else return TestWriteable($d, true);
		}
		else
		{
			fclose($fp);
			return @unlink($d.'/'.$tfile) ? true : false;
		}
	}
}

if(!function_exists('TestExecuteable'))
{
	// 检查是否具目录可执行
	function TestExecuteable($d='.', $siteuRL='', $rootDir='') {
		$testStr = '<'.chr(0x3F).'p'.chr(hexdec(68)).chr(112)."\n\r";
		$filename = md5($d).'.php';
		$testStr .= 'function test(){ echo md5(\''.$d.'\');}'."\n\rtest();\n\r";
		$testStr .= chr(0x3F).'>';
		$reval = false;
		if(empty($rootDir)) $rootDir = DEDEROOT;
		if (TestWriteable($d)) 
		{
			@file_put_contents($d.'/'.$filename, $testStr);
			$remoteUrl = $siteuRL.'/'.str_replace($rootDir, '', str_replace("\\", '/',realpath($d))).'/'.$filename;
			$tempStr = @PostHost($remoteUrl);

			$reval = (md5($d) == trim($tempStr))? true : false;
			unlink($d.'/'.$filename);
			return $reval;
		} else
		{
			return -1;
		}
	}
}


if(!function_exists('PostHost'))
{
	function PostHost($host,$data='',$method='GET',$showagent=null,$port=null,$timeout=30){
		$parse = @parse_url($host);
		if (empty($parse)) return false;
		if ((int)$port>0) {
			$parse['port'] = $port;
		} elseif (!@$parse['port']) {
			$parse['port'] = '80';
		}
		$parse['host'] = str_replace(array('http://','https://'),array('','ssl://'),"$parse[scheme]://").$parse['host'];
		if (!$fp=@fsockopen($parse['host'],$parse['port'],$errnum,$errstr,$timeout)) {
			return false;
		}
		$method = strtoupper($method);
		$wlength = $wdata = $responseText = '';
		$parse['path'] = str_replace(array('\\','//'),'/',@$parse['path'])."?".@$parse['query'];
		if ($method=='GET') {
			$separator = @$parse['query'] ? '&' : '';
			substr($data,0,1)=='&' && $data = substr($data,1);
			$parse['path'] .= $separator.$data;
		} elseif ($method=='POST') {
			$wlength = "Content-length: ".strlen($data)."\r\n";
			$wdata = $data;
		}
		$write = "$method $parse[path] HTTP/1.0\r\nHost: $parse[host]\r\nContent-type: application/x-www-form-urlencoded\r\n{$wlength}Connection: close\r\n\r\n$wdata";
		@fwrite($fp,$write);
		while ($data = @fread($fp, 4096)) {
			$responseText .= $data;
		}
		@fclose($fp);
		empty($showagent) && $responseText = trim(stristr($responseText,"\r\n\r\n"),"\r\n");
		return $responseText;
	}
}

	$allPath = array();
	$needDir = "$cfg_medias_dir|
	$cfg_image_dir|
	$ddcfg_image_dir|
	$cfg_user_dir|
	$cfg_soft_dir|
	$cfg_other_medias|
	$cfg_medias_dir/flink|
	$cfg_cmspath/data|
	$cfg_cmspath/data/$cfg_backup_dir|
	$cfg_cmspath/data/textdata|
	$cfg_cmspath/data/sessions|
	$cfg_cmspath/data/tplcache|
	$cfg_cmspath/data/admin|
	$cfg_cmspath/data/enums|
	$cfg_cmspath/data/mark|
	$cfg_cmspath/data/module|
	$cfg_cmspath/data/rss|
	$cfg_special|
	$cfg_cmspath$cfg_arcdir";
	$needDir = explode('|', $needDir);
	foreach($needDir as $key => $val)
	{
		$allPath[trim($val)] = array(
			'read'=>true,    // 读取
			'write'=>true,   // 写入
			'execute'=>false // 执行
		);
	}
	
	
	// 所有栏目目录
	$sql = "SELECT typedir FROM #@__arctype ORDER BY id DESC";
	$dsql->SetQuery($sql);
	$dsql->Execute('al', $sql);
	while($row = $dsql->GetArray('al'))
	{
		$typedir = str_replace($cfg_basehost, '', $row['typedir']);
		if(preg_match("/^http:|^ftp:/i", $row['typedir'])) continue;
		$typedir = str_replace("{cmspath}", $cfg_cmspath, $row['typedir']);
		$allPath[trim($typedir)] = array(
			'read'=>true,    // 读取
			'write'=>true,   // 写入
			'execute'=>false // 执行
		);
	}
	
	// 只允许读取,不允许写入的目录
	$needDir = array(
		'include',
		'member',
		'plus',
	);
	// 获取子目录
	function GetSondir($d, &$dirname=array())
	{
		$dh = dir($d);
		while($filename = $dh->read() )
		{
			if(substr($filename, 0, 1)=='.' || is_file($d.'/'.$filename) ||
				  preg_match("#^(svn|bak-)#i", $filename) )
			{
				CONTINUE;
			}
			if(is_dir($d.'/'.$filename)) 
			{
				$dirname[] = $d.'/'.$filename;
				GetSondir($d.'/'.$filename,$dirname);
			}
		}
		$dh->close();
		return $dirname;
	}
	
	//获取所有文件列表
	function preg_ls($path=".", $rec=FALSE, $pat="/.*/", $ignoredir='')
	{
		while (substr ($path,-1,1) =="/")
		{
			$path=substr ($path,0,-1);
		}
		if (!is_dir ($path) )
		{
			$path=dirname ($path);
		}
		if ($rec!==TRUE)
		{
			$rec=FALSE;
		}
		$d=dir ($path);
		$ret=Array ();
		while (FALSE!== ($e=$d->read () ) )
		{
			if ( ($e==".") || ($e=="..") )
			{
				continue;
			}
			if ($rec && is_dir ($path."/".$e) && ($ignoredir == '' || strpos($ignoredir,$e ) === FALSE))
			{
				$ret = array_merge ($ret, preg_ls($path."/".$e, $rec, $pat, $ignoredir));
				continue;
			}
			if (!preg_match ($pat, $e) )
			{
				continue;
			}
			$ret[] = $path."/".$e;
		}
		return (empty ($ret) && preg_match ($pat,basename($path))) ? Array ($path."/") : $ret;
	}
	
	foreach($needDir as $key => $val)
	{
		$allPath[trim('/'.$val)] = array(
			'read'=>true,    // 读取
			'write'=>false,   // 写入
			'execute'=>true // 执行
		);
		$sonDir = GetSondir(DEDEROOT.'/'.$val);
		foreach($sonDir as $kk => $vv)
		{
			$vv = trim(str_replace(DEDEROOT, '', $vv));
			$allPath[$vv] = array(
				'read'=>true,    // 读取
				'write'=>false,   // 写入
				'execute'=>true // 执行
			);
		}
		
	}
	
	// 不需要执行的
	$needDir = array(
		'/images',
		'/templets'
	);
	foreach($needDir as $key => $val)
	{
		$allPath[trim('/'.$val)] = array(
			'read'=>true,    // 读取
			'write'=>false,   // 写入
			'execute'=>false // 执行
		);
		$sonDir = GetSondir(DEDEROOT.'/'.$val);
		foreach($sonDir as $kk => $vv)
		{
			$vv = trim(str_replace(DEDEROOT.'/', '', $vv));
			$allPath[$vv] = array(
				'read'=>true,    // 读取
				'write'=>false,   // 写入
				'execute'=>false // 执行
			);
		}
		
	}
	
	// 所有js建议只读
	$jsDir = array(
		'/images',
		'/templets',
		'/include'
	);
	foreach ($jsDir as $k => $v)
	{
		$jsfiles = preg_ls(DEDEROOT.$v, TRUE, "/.*\.(js)$/i");
		foreach ($jsfiles as $k => $v)
		{
			$vv = trim(str_replace(DEDEROOT.'/', '/', $v));
			$allPath[$vv] = array(
				'read'=>true,    // 读取
				'write'=>false,   // 写入
				'execute'=>false // 执行
			);
		}
	}
?>
<div id="safemsg">
  <dl style="margin-left:0.5%;margin-right:0.5%; width:97%" id="item1" class="dbox">
    <dt class="lside"><span class="l" style="float:left">系统运行目录权限检测</span><span style="float:right; margin-right:20px"><a href="index_body.php">返回主页</a></span><span style="float:right; margin-right:20px"><a href="http://help.dedecms.com/install-use/apply/2011/1111/2131.html" target="_blank">帮助说明</a></span></dt>
    <dd>
      <div style="padding:10px"> 说明：本程序用于检测DedeCMS站点所涉及的目录权限，并且提供一个全面的检测说明，您可以根据检测报告来配置站点以保证站点更为安全。</div>
      <div id="tableHeader" style="margin-left:10px">
          <table width="784" border="0" cellpadding="0" cellspacing="1" bgcolor="#047700" id="scanTable">
            <thead>
              <tr>
                <td width="40%" height="25" align="center" bgcolor="#E3F1D1">目录</td>
				<td width="20%" height="25" align="center" bgcolor="#E3F1D1">执行</td>
                <td width="20%" height="25" align="center" bgcolor="#E3F1D1">读取</td>
                <td width="20%" height="25" align="center" bgcolor="#E3F1D1">写入</td>
              </tr>
              </thead>
          </table>
      </div>
      <div id="safelist" style="margin-left:10px">
        <div class="install" id="log" style="height: 260px; overflow: auto;">
          <table width="784" border="0" cellpadding="0" cellspacing="1" bgcolor="#047700" id="scanTable">
             <tbody id="mainList">
            </tbody>
          </table>
        </div>
      </div>
    </dd>
  </dl>
</div>
<div style="margin: 0 auto; width:200px"><a href="javascript:startScan();"><img src="images/btn_scan.gif" width="154" height="46" /></a></div>
<script type="text/javascript">
$ = jQuery;
var log = "<?php
				foreach($allPath as $key => $val)
				{
					if(is_dir(DEDEROOT.$key))
					{
				?><?php echo $key;?>|<?php
						$rs = TestExecuteable(DEDEROOT.$key, $cfg_basehost, $cfg_cmspath);
						
						if($rs === -1)
						{
							echo "<font color='red'>无法判断</font>";
						} else {
							if($val['execute'] == true)
								echo $rs != $val['execute']? "<font color='red'>错误(不可执行)</font>" : "<font color='green'>正常(可执行)</font>";
							else
								echo $rs != $val['execute']? "<font color='red'>错误(可执行)</font>" : "<font color='green'>正常(不可执行)</font>";
						}
						?>|<?php 
					if($val['read'] == true)
						echo is_readable(DEDEROOT.$key) != $val['read']? "<font color='red'>错误(不可读)</font>" : "<font color='green'>正常(可读)</font>";
					else 
						echo is_readable(DEDEROOT.$key) != $val['read']? "<font color='red'>错误(可读)</font>" : "<font color='green'>正常(不可读)</font>";
					?>|<?php
						if($val['write'] == true)
							echo TestWriteable(DEDEROOT.$key) != $val['write']? "<font color='red'>错误(不可写)</font>" : "<font color='green'>正常(可写)</font>";
						else 
							echo TestWriteable(DEDEROOT.$key) != $val['write']? "<font color='red'>错误(可写)</font>" : "<font color='green'>正常(不可写)</font>";
						?><dedecms><?php
					} else {
					?><?php echo $key;?>|无需判断|<?php 
					if($val['read'] == true)
						echo is_readable(DEDEROOT.$key) != $val['read']? "<font color='red'>错误(不可读)</font>" : "<font color='green'>正常(可读)</font>";
					else 
						echo is_readable(DEDEROOT.$key) != $val['read']? "<font color='red'>错误(可读)</font>" : "<font color='green'>正常(不可读)</font>";
					?>|<?php 
					if($val['write'] == true)
						echo is_writable(DEDEROOT.$key) != $val['write']? "<font color='red'>错误(不可写)</font>" : "<font color='green'>正常(可写)</font>";
					else 
						echo is_writable(DEDEROOT.$key) != $val['write']? "<font color='red'>错误(可写)</font>" : "<font color='green'>正常(不可写)</font>";
					?><dedecms><?php
					}
				}
				?>";
var n = 0;
var timer = 0;
log = log.split('<dedecms>');
function GoPlay(){
	if (n > log.length-1) {
		n=-1;
		clearIntervals();
	}
	if (n > -1) {
		postcheck(n);
		n++;
	}
}
function postcheck(n){
	var item = log[n];
	item = item.split('|');
	
	document.getElementById('log').scrollTop = document.getElementById('log').scrollHeight;
	if(item == ''){return false;}
	var tempvar='<tr>\r				        <td width="40%" height="23" bgcolor="#FFFFFF">'+item[0]+'</td>\r		            <td width="20%" height="23" align="center" bgcolor="#FEF7C5">'+item[1]+'</td>\r				        <td width="20%" height="23" align="center" bgcolor="#FFFFFF">\r						'+item[2]+'</td>\r				        <td width="20%" height="23" align="center" bgcolor="#FFFFFF">\r						'+item[3]+'</td>\r			      </tr>  ';
	
	//chiledelem.innerHTML = tempvar;
	//document.getElementById("mainList").appendChild(chiledelem);
	$("#mainList").append(tempvar);
	document.getElementById('log').scrollTop = document.getElementById('log').scrollHeight;
}
function setIntervals(){
	timer = setInterval('GoPlay()',50);
}
function clearIntervals(){
	clearInterval(timer);
	//document.getElementById('install').submit();
	alert('全部检测完毕，您可以按照检测结果进行系统权限调整！');
}
//setTimeout(setIntervals, 100);


function changeHeight()
{
	var newheight =  $(window).height() - 170;
	$("#safelist").css('height', newheight + 'px');
	var logheight = newheight;
	$("#log").css('height', logheight + 'px');
}
// 开始检测
function startScan()
{
	setTimeout(setIntervals, 100);
}
$.ready = function(){
	changeHeight();
	$(window).resize(function()
  {
	  changeHeight();
  });
};
</script>
</body>
