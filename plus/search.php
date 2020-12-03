<?
require_once(dirname(__FILE__)."/../include/inc_arcsearch_view.php");

if(empty($typeid)) $typeid = 0;
else $typeid = ereg_replace("[^0-9]","",$typeid);

if(empty($orderby)) $orderby="";
else $orderby = eregi_replace("[^a-z]","",$orderby);

if(empty($channeltype)) $channeltype="-1";
else $channeltype = eregi_replace("[^0-9]","",$channeltype);

if(empty($searchtype)) $searchtype = "titlekeyword";
else $searchtype = eregi_replace("[^a-z]","",$searchtype);

//每页显示的结果数，在用户没指定的情况下用10
if(empty($pagesize)) $pagesize = 10;
else $pagesize = eregi_replace("[^0-9]","",$pagesize);

if(!isset($kwtype)) $kwtype = 1;

if(empty($keyword)) $keyword = "";

$keyword = stripslashes($keyword);
$keyword = ereg_replace("[\|\"\r\n\t%\*\?\(\)\$;,'%-]"," ",trim($keyword));

if(eregi($cfg_notallowstr,$keyword) || eregi($cfg_replacestr,$keyword)){
	echo "你的信息中存在非法内容，被系统禁止！<a href='javascript:history.go(-1)'>[返回]</a>"; exit();
}

if($keyword==""||strlen($keyword)<2){
	ShowMsg("关键字不能小于2个字节！","-1");
	exit();
}

if(empty($starttime)) $starttime = -1;
else //开始时间
{
	$starttime = ereg_replace("[^0-9]","",$starttime);
	if($starttime>0){
	  $starttime = ereg_replace("[^0-9]","",$starttime);
	  $dayst = GetMkTime("2006-1-2 0:0:0") - GetMkTime("2006-1-1 0:0:0");
	  $starttime = mytime() - ($starttime * $dayst);
  }
}

$sp = new SearchView($typeid,$keyword,$orderby,$channeltype,$searchtype,$starttime,$pagesize,$kwtype);
$sp->Display();
$sp->Close();

?>