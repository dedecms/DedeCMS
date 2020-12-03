<?
require_once(dirname(__FILE__)."/config.php");
require_once(dirname(__FILE__)."/../include/inc_typelink.php");
require_once(dirname(__FILE__)."/../include/pub_datalist_dm.php");
require_once(dirname(__FILE__)."/inc/inc_list_functions.php");
setcookie("ENV_GOBACK_URL",$dedeNowurl,time()+3600,"/");

if(!isset($cid)) $cid = 0;
if(!isset($keyword)) $keyword = "";
if(!isset($channelid)) $channelid = 0;
if(!isset($arcrank)) $arcrank = "";
$typeid = $cid;

$tl = new TypeLink($cid);
$optionarr = $tl->GetOptionArray($cid,$cuserLogin->getUserChannel(),$channelid);

$whereSql = " where #@__archives.channel > 0 ";

if($keyword!=""){
	$whereSql .= " And (title like '%$keyword%' Or writer like '%$keyword%' Or source like '%$keyword%') ";
}

if($typeid!=0){
	$tlinkSql = $tl->GetSunID($typeid,"#@__archives",0);
	$whereSql .= " And $tlinkSql ";
}

$tl->Close();

$query = "
select #@__archives.ID,#@__archives.typeid,#@__archives.senddate,#@__archives.iscommend,#@__archives.ismake,#@__archives.channel,#@__archives.arcrank,#@__archives.click,#@__archives.title,#@__archives.color,#@__archives.litpic,#@__archives.pubdate,#@__archives.adminID,#@__archives.memberID,#@__arctype.typename,#@__channeltype.typename as channelname 
from #@__archives 
left join #@__arctype on #@__arctype.ID=#@__archives.typeid
left join #@__channeltype on #@__channeltype.ID=#@__archives.channel
$whereSql
order by ID desc
";

$dlist = new DataList();
$dlist->pageSize = 20;
$dlist->SetParameter("dopost","listArchives");
$dlist->SetParameter("keyword",$keyword);
$dlist->SetParameter("cid",$cid);
$dlist->SetParameter("f",$f);
$dlist->SetParameter("arcrank",$arcrank);
$dlist->SetParameter("channelid",$channelid);
$dlist->SetSource($query);
include(dirname(__FILE__)."/templets/content_select_list.htm");
$dlist->Close();
?>