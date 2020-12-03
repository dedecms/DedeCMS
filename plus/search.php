<?php
require_once(dirname(__FILE__)."/../include/inc_arcsearch_view.php");

$timestamp = time();
$timelock = '../data/time.lock';
if($cfg_allsearch_limit < 1) $cfg_allsearch_limit = 1;
if(file_exists($timelock)){
	if($timestamp - filemtime($timelock) < $cfg_allsearch_limit){
		showmsg('服务器忙，请稍后搜索','-1');
		exit();
	}
}
@touch($timelock,$timestamp);

$channelid = isset($channelid) && is_numeric($channelid) ? $channelid : 0;

if(empty($typeid)) $typeid = 0;
else $typeid = ereg_replace("[^0-9]","",$typeid);

if($typeid){
	$channelid = 0;
}

if(!isset($searchtype)) $searchtype = '';
if($searchtype != 'titlekeyword') $searchtype = "title";

$cacheid = isset($cacheid) && is_numeric($cacheid) ? $cacheid : 0;
$kwtype = isset($kwtype) && $kwtype == 0 ? 0 : 1;

$keyword = stripslashes($keyword);
if(ereg("[><]",$keyword)){
	ShowMsg("你的关键词输入不合法！","-1");
	exit();
}
$keyword = ereg_replace("[\|\"\r\n\f\t%\*\?\(\)\$;,'%-><]"," ",trim($keyword));

if( ($cfg_notallowstr!='' && eregi($cfg_notallowstr,$keyword)) || ($cfg_replacestr!='' && eregi($cfg_replacestr,$keyword)) ){
	echo "你的信息中存在非法内容，被系统禁止！<a href='javascript:history.go(-1)'>[返回]</a>"; exit();
}

if($keyword=="" || strlen($keyword) < 3 || strlen($keyword) > 30){
	ShowMsg("关键字长度必须要3-30字节之间！","-1");
	exit();
}


$sp = new SearchView($typeid,$keyword,$channelid,$searchtype,$kwtype,$cacheid);
$sp->Display();
$sp->Close();

?>