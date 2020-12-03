<?php 
require_once(dirname(__FILE__).'/config.php');
CheckPurview('sys_Edit');
if(empty($action)) $action = '';
if(empty($message)) $message = '尚未进行检测……';

$safefile = "data/common.inc.php
dede/config.php
dede/index_body.php
dede/member_do.php
dede/sys_info_pay.php
group/postform.php
group/reply.php
include/common.inc.php
include/mail.class.php
member/paycenter/alipay/alipay_notify.php
member/paycenter/alipay/notify_url.php
member/paycenter/alipay/return_url.php
member/paycenter/cbpayment/autoreceive.php
member/paycenter/nps/pay_back_nps.php
member/paycenter/tenpay/tenpay_gbk_page.php
member/paycenter/yeepay/yeepay_gbk.php
plus/paycenter/alipay/alipay_notify.php
plus/paycenter/alipay/notify_url.php
plus/paycenter/alipay/return_url.php
plus/paycenter/cbpayment/autoreceive.php
plus/paycenter/cbpayment/receive.php
plus/paycenter/nps/pay_back_nps.php
plus/paycenter/yeepay/yeepay_gbk.php
include/dedecollection.class.php
include/dedetag.class.php
include/dialog/config.php
include/FCKeditor/fckeditor.php
include/smtp.class.php
include/zip.class.php
install/common.inc.php
install/index.php";

$adminDir = ereg_replace("(.*)[/\\]","",dirname(__FILE__));
$safefile = trim(str_replace('dede/',$adminDir.'/',$safefile));
$safefiles = split("[\r\n]{1,}",$safefile);

function TestOneFile($f)
{
	global $message;
	$str = '';
	
	//排除safefile和data/tplcache目录
	if(NotCheckFile($f) || ereg('data/tplcache',$f)) return -1;
	
	$fp = fopen($f,'r');
	while(!feof($fp)) { $str .= fgets($fp,1024); }
	fclose($fp);
	if(eregi("(eval|cmd|_GET|_POST)[ \r\n\t]{0,}([\[\(])",$str))
	{
		$trfile = ereg_replace('^'.DEDEROOT,'',$f);
		$message .= "<div style='clear:both;border-bottom:1px dotted #B8E6A2;line-height:24px'>
		<div style='width:350px;float:left'>可疑文件：{$trfile}</div>
		<div style='float:left'>[<a href='file_manage_view.php?fmdo=del&filename=$trfile&activepath=' target='_blank'><u>删除</u></a>]
		[<a href='file_manage_view.php?fmdo=edit&filename=$trfile&activepath=' target='_blank'><u>查看源码</u></a>]
		</div></div>\r\n";
		return 1;
	}
	return 0;
}

function NotCheckFile($f)
{
	global $safefiles, $safefile;
	if($safefile != '')
	{
	   foreach($safefiles as $v)
	   {
	      //if(empty($v)) continue;
	      if( eregi($v,$f) ) return true;
	   }
	}
	return false;
}

function TestSafe($tdir)
{
	 $dh = dir($tdir);
	 while($fname=$dh->read())
	 {
	 	  $fnamef = $tdir.'/'.$fname;
	 	  if(is_dir($fnamef) && $fname != '.' && $fname != '..')
	 	  {
	 	  	TestSafe($fnamef);
	 	  }
	 	  if(eregi("\.(php|inc)",$fnamef))
	 	  {
	 	  	TestOneFile($fnamef);
	 	  }
	 }
}

//检测
if($action=='test')
{
	 $message = '';
	 AjaxHead();
	 TestSafe(DEDEROOT);
	 if($message=='') $message = "<font color='green' style='font-size:14px'>没发现可疑文件！</font>";
	 echo $message;
	 exit();
}
//清空模板缓存
else if($action=='clear')
{
	 global $cfg_tplcache_dir;
	 $message = '';
	 $d = DEDEROOT.$cfg_tplcache_dir;
	 AjaxHead();
	 sleep(1);
	 if(ereg('data/',$cfg_tplcache_dir) && file_exists($d) && is_dir($d))
	 {
	   $dh = dir($d);
	   while($filename = $dh->read())
	   {
	 	    if($filename=='.'||$filename=='..'||$filename=='index.html') continue;
	 	    @unlink($d.'/'.$filename);
	   }
	 }
	 $message = "<font color='green' style='font-size:14px'>成功清空模板缓存！</font>";
	 echo $message;
	 exit();
}

include(dirname(__FILE__).'/templets/sys_safetest.htm');

?>