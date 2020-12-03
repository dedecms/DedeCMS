<?
require_once(dirname(__FILE__)."/config.php");
require_once(dirname(__FILE__)."/../include/inc_typelink.php");
require_once(dirname(__FILE__)."/../include/pub_datalist.php");
require_once(dirname(__FILE__)."/inc/inc_list_functions.php");
setcookie("ENV_GOBACK_URL",$dedeNowurl,time()+3600,"/");

if(!isset($cid)) $cid = 0;
if(!isset($keyword)) $keyword = "";
if(!isset($channelid)) $channelid = 0;
if(!isset($arcrank)) $arcrank = "";
$typeid = $cid;
if($cuserLogin->getUserRank()<5) $arcrank = -1;

$tl = new TypeLink($cid);

if($cid==0){
	if($channelid==0) $positionname = "所有栏目&gt;";
	else{
		$row = $tl->dsql->GetOne("Select typename From #@__channeltype where ID='$channelid'");
		$positionname = $row[0]."&gt;";
	}
}else{
	$positionname = str_replace($cfg_list_symbol,"&gt;",$tl->GetPositionName())."&gt;";
}

$optionarr = $tl->GetOptionArray($cid,$cuserLogin->getUserChannel(),$channelid);

if($channelid==0) $whereSql = " where #@__archives.channel > 0 ";
else $whereSql = " where #@__archives.channel = '$channelid' ";

if($keyword!=""){
	$whereSql .= " And (title like '%$keyword%' Or writer like '%$keyword%' Or source like '%$keyword%') ";
}

if($typeid!=0){
	$tlinkSql = $tl->GetSunID($typeid,"#@__archives",0);
	$whereSql .= " And $tlinkSql ";
}

if($arcrank!=""){
	$whereSql .= " And arcrank=$arcrank ";
	$CheckUserSend = "<input type='button' onClick=\"location='catalog_do.php?cid=".$cid."&dopost=listArchives&gurl=content_list.php';\" value='所有文档'>";
}
else
{
	$CheckUserSend = "<input type='button' onClick=\"location='catalog_do.php?cid=".$cid."&dopost=listArchives&arcrank=-1&gurl=content_list.php';\" value='稿件审核'>";
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
$dlist->SetParameter("dopost","listArchives");
$dlist->SetParameter("keyword",$keyword);
$dlist->SetParameter("cid",$cid);
$dlist->SetParameter("arcrank",$arcrank);
$dlist->SetParameter("channelid",$channelid);
$dlist->SetTemplet(dirname(__FILE__)."/templets/content_list.htm");
$dlist->SetSource($query);
$dlist->Display();
$dlist->Close();
?>