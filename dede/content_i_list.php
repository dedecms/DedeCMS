<?
require_once(dirname(__FILE__)."/config.php");
if(!isset($cid)) $cid = 0;
if(!isset($keyword)) $keyword = "";
if(!isset($channelid)) $channelid = 0;
if(!isset($arcrank)) $arcrank = "";
if(!isset($adminid)) $adminid = 0;
//检查权限许可，总权限
CheckPurview('a_List,a_AccList,a_MyList');
//栏目浏览许可
if(TestPurview('a_List')){ ; }
else if(TestPurview('a_AccList')){
	 if($cid==0) $cid = $cuserLogin->getUserChannel();
	 else CheckCatalog($cid,"你无权浏览非指定栏目的内容！");
}else{
	 $adminid = $cuserLogin->getUserID();
}
//----------------------------------------------------------
require_once(dirname(__FILE__)."/../include/inc_typelink.php");
require_once(dirname(__FILE__)."/../include/pub_datalist_dm.php");
require_once(dirname(__FILE__)."/inc/inc_list_functions.php");
setcookie("ENV_GOBACK_URL",$dedeNowurl,time()+3600,"/");


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

if($cid!=0){
	$tlinkSql = $tl->GetSunID($cid,"#@__archives",0);
	$whereSql .= " And $tlinkSql ";
}

if($adminid>0){ $whereSql .= " And #@__archives.adminID = '$adminid' "; }

if($arcrank!=""){
	$whereSql .= " And arcrank=$arcrank ";
	$CheckUserSend = "<input type='button' onClick=\"location='catalog_do.php?cid=".$cid."&dopost=listArchives&gurl=content_list.php';\" value='所有文档'>";
}
else{
	$CheckUserSend = "<input type='button' onClick=\"location='catalog_do.php?cid=".$cid."&dopost=listArchives&arcrank=-1&gurl=content_list.php';\" value='稿件审核'>";
}

$tl->Close();

$query = "
select #@__archives.ID,#@__archives.adminID,#@__archives.typeid,#@__archives.senddate,#@__archives.iscommend,#@__archives.ismake,#@__archives.channel,#@__archives.arcrank,#@__archives.click,#@__archives.title,#@__archives.color,#@__archives.litpic,#@__archives.pubdate,#@__archives.adminID,#@__archives.memberID,#@__arctype.typename,#@__channeltype.typename as channelname,#@__admin.uname as adminname 
from #@__archives 
left join #@__arctype on #@__arctype.ID=#@__archives.typeid
left join #@__channeltype on #@__channeltype.ID=#@__archives.channel
left join #@__admin on #@__admin.ID=#@__archives.adminID
$whereSql
order by #@__archives.ID desc
";

$dlist = new DataList();
$dlist->pageSize = 10;
$dlist->SetParameter("dopost","listArchives");
$dlist->SetParameter("keyword",$keyword);
$dlist->SetParameter("cid",$cid);
$dlist->SetParameter("channelid",$channelid);
$dlist->SetParameter("arcrank",$arcrank);
$dlist->SetSource($query);
include(dirname(__FILE__)."/templets/content_i_list.htm");
$dlist->Close();
?>