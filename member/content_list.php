<?
require_once(dirname(__FILE__)."/config.php");
CheckRank(0,0);

if(!isset($cid)) $cid = 0;
if(!isset($keyword)) $keyword = "";
if(!isset($channelid)) $channelid = 0;
if(!isset($arcrank)) $arcrank = "";
$positionname = "";

$memberid = $cfg_ml->M_ID;

////////////////////////////////////////////////////////////////////
require_once(dirname(__FILE__)."/../include/inc_typelink.php");
require_once(dirname(__FILE__)."/../include/pub_datalist_dm.php");
require_once(dirname(__FILE__)."/inc/inc_list_functions.php");
setcookie("ENV_GOBACK_URL",$dedeNowurl,time()+3600,"/");

$cid = ereg_replace("[^0-9]","",$cid);
$channelid = ereg_replace("[^0-9]","",$channelid);
$tl = new TypeLink($cid);

$cInfos = $tl->dsql->GetOne("Select arcsta From #@__channeltype  where ID='$channelid'; ");
$arcsta = $cInfos['arcsta'];


if($cid==0){
	$row = $tl->dsql->GetOne("Select typename From #@__channeltype where ID='$channelid'");
	if(is_array($row)) $positionname = $row[0]." &gt;&gt; ";
}else{
	$positionname = str_replace($cfg_list_symbol," &gt;&gt; ",$tl->GetPositionName())." &gt;&gt; ";
}

$whereSql = " where #@__archives.channel = '$channelid' And #@__archives.memberID='$memberid' ";

if(!empty($mtype)){
	$mtype = ereg_replace("[^0-9]","",$mtype);
	$whereSql .= " And (#@__archives.mtype='$mtype') ";
}

if($keyword!=""){
	$keyword = cn_substr(trim(ereg_replace("[\|\"\r\n\t%\*\.\?\(\)\$ ;,'%-]","",stripslashes($keyword))),30);
  $keyword = addslashes($keyword);
	$whereSql .= " And (#@__archives.title like '%$keyword%') ";
}

if($cid!=0){
	$tlinkSql = $tl->GetSunID($cid,"#@__archives",0);
	$whereSql .= " And $tlinkSql ";
}

$tl->Close();

$query = "
select #@__archives.ID,#@__archives.adminID,#@__archives.typeid,#@__archives.senddate,
#@__archives.iscommend,#@__archives.ismake,#@__archives.channel,#@__archives.arcrank,
#@__archives.click,#@__archives.title,#@__archives.color,#@__archives.litpic,#@__archives.pubdate,
#@__archives.adminID,#@__archives.memberID,#@__arctype.typename,
#@__channeltype.typename as channelname 
from #@__archives 
left join #@__arctype on #@__arctype.ID=#@__archives.typeid
left join #@__channeltype on #@__channeltype.ID=#@__archives.channel
$whereSql
order by #@__archives.senddate desc
";

$dlist = new DataList();
$dlist->pageSize = 20;
$dlist->SetParameter("dopost","listArchives");
$dlist->SetParameter("keyword",$keyword);
$dlist->SetParameter("cid",$cid);
$dlist->SetParameter("channelid",$channelid);
$dlist->SetSource($query);
include(dirname(__FILE__)."/templets/content_list.htm");
$dlist->Close();

if(isset($dsql) && is_object($dsql)) $dsql->Close();
?>