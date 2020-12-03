<?php
require_once(dirname(__FILE__)."/../include/inc_arcsearch_view.php");

$timestamp = time();
$timelock = '../data/time.lock';
if(file_exists($timelock)){
	if($timestamp - filemtime($timelock) < $cfg_allsearch_limit){
		showmsg('服务器忙，请稍后搜索','-1');
		exit();
	}
}
@touch($timelock,$timestamp);

if(!isset($channelid)) $channelid = 0;
$channelid = intval($channelid);

if(empty($typeid)) $typeid = 0;
else $typeid = ereg_replace("[^0-9]","",$typeid);

if($typeid){
	$dsql = new DedeSql(false);
	$row = $dsql->GetOne("Select channeltype From #@__arctype where ID='{$typeid}' ");
	if(is_array($row)){
		if(!empty($channelid) && $row['channeltype'] != $channelid) {
			$dsql->Close();
			showmsg('选定栏目和频道类型不符，请重新选择搜索条件','-1');
			exit();
		}
	}else{
		showmsg('栏目不正确，请重新选择搜索条件','-1');
		exit();
	}
}

if($searchtype != 'titlekeyword') $searchtype = "title";
if(!isset($cacheid)) $cacheid = 0;
$cacheid = intval($cacheid);
if(!isset($kwtype)) $kwtype = 0;
if($kwtype != 1) $kwtype = 0;

$keyword = stripslashes($keyword);
$keyword = ereg_replace("[\|\"\r\n\t%\*\?\(\)\$;,'%-]"," ",trim($keyword));

if( ($cfg_notallowstr!='' && eregi($cfg_notallowstr,$keyword)) || ($cfg_replacestr!='' && eregi($cfg_replacestr,$keyword)) ){
	echo "你的信息中存在非法内容，被系统禁止！<a href='javascript:history.go(-1)'>[返回]</a>"; exit();
}

if($keyword==""||strlen($keyword)<3){
	ShowMsg("关键字不能小于3个字节！","-1");
	exit();
}


$sp = new SearchView($typeid,$keyword,$channelid,$searchtype,$kwtype,$cacheid);
$sp->Display();
$sp->Close();

?>