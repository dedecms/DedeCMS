<?php
require_once(dirname(__FILE__)."/config.php");
CheckRank(0,0);
require_once(DEDEINC."/typelink.class.php");
require_once(DEDEINC."/datalistcp.class.php");
require_once(DEDEMEMBER."/inc/inc_list_functions.php");
setcookie("ENV_GOBACK_URL",$dedeNowurl,time()+3600,"/");
$cid = isset($cid) && is_numeric($cid) ? $cid : 0;
$channelid = isset($channelid) && is_numeric($channelid) ? $channelid : 0;
$mtypesid = isset($mtypesid) && is_numeric($mtypesid) ? $mtypesid : 0;
if(!isset($keyword))
{
	$keyword = '';
}
if(!isset($arcrank))
{
	$arcrank = '';
}
$positionname = '';
$menutype = 'content';
$mid = $cfg_ml->M_ID;
$tl = new TypeLink($cid);
$cInfos = $tl->dsql->GetOne("Select arcsta,issend,issystem,usertype,typename,addtable From `#@__channeltype`  where id='$channelid'; ");
if(!is_array($cInfos))
{
	ShowMsg('模型不存在', '-1');
	exit();
}
$arcsta = $cInfos['arcsta'];

//禁止访问无权限的模型
if($cInfos['usertype'] !='' && $cInfos['usertype']!=$cfg_ml->M_MbType)
{
	ShowMsg('你无权限访问该部分', '-1');
	exit();
}

if($cid==0)
{
	$positionname = $cInfos['typename']." &gt;&gt; ";
}
else
{
	$positionname = str_replace($cfg_list_symbol," &gt;&gt; ",$tl->GetPositionName())." &gt;&gt; ";
}
$whereSql = " where arc.channel = '$channelid' And arc.mid='$mid' ";
if($keyword!='')
{
	$keyword = cn_substr(trim(ereg_replace("[><\|\"\r\n\t%\*\.\?\(\)\$ ;,'%-]","",stripslashes($keyword))),30);
	$keyword = addslashes($keyword);
	$whereSql .= " And (arc.title like '%$keyword%') ";
}
if($cid!=0)
{
	$whereSql .= " And arc.typeid in (".GetSonIds($cid).")";
}

$query = "select arc.aid,arc.aid as id,arc.typeid,arc.senddate,arc.channel,arc.click,arc.title,arc.mid,tp.typename
        from `{$cInfos['addtable']}` arc
        left join `#@__arctype` tp on tp.id=arc.typeid
        $whereSql
        order by arc.aid desc ";
$dlist = new DataListCP();
$dlist->pageSize = 20;
$dlist->SetParameter("dopost","listArchives");
$dlist->SetParameter("keyword",$keyword);
$dlist->SetParameter("cid",$cid);
$dlist->SetParameter("channelid",$channelid);
$dlist->SetTemplate(DEDEMEMBER."/templets/content_sg_list.htm");
$dlist->SetSource($query);
$dlist->Display();
?>