<?php 
require_once(dirname(__FILE__)."/config.php");
require_once(dirname(__FILE__)."/../include/pub_datalist_dm.php");
require_once(dirname(__FILE__)."/../include/inc_typelink.php");
setcookie("ENV_GOBACK_URL",$dedeNowurl,time()+3600,"/");
$bgcolor = "";
if(!isset($keyword)) $keyword="";
if(!isset($typeid)) $typeid="0";

function IsCheck($st)
{
	if($st==1) return "[“—…Û∫À]";
	else return "<font color='red'>[Œ¥…Û∫À]</font>";
}

$tl = new TypeLink($typeid);

$seltypeids = 0;
if(!empty($typeid)){
	$seltypeids = $tl->dsql->GetOne("Select ID,typename,channeltype From #@__arctype where ID='$typeid' ");
}
$opall=1;
if(is_array($seltypeids)){
	$optionarr = GetTypeidSel('form1','typeid','selbt1',0,$seltypeids['ID'],$seltypeids['typename']);
}else{
	$optionarr = GetTypeidSel('form1','typeid','selbt1',0,0,'«Î—°‘Ò...');
}

if($cuserLogin->getUserChannel()<=0) $typeCallLimit = "";
else $typeCallLimit = "And ".$tl->getSunID($cuserLogin->getUserChannel(),"");

if($typeid!=0) $arttypesql = " And ".$tl->getSunID($typeid,"");
else $arttypesql = "";

$querystring = "select * from #@__feedback where CONCAT(#@__feedback.msg,#@__feedback.arctitle) like '%$keyword%' $arttypesql $typeCallLimit order by dtime desc";

$dlist = new DataList();
$dlist->pageSize = 10;
$dlist->Init();
$dlist->SetParameter("typeid",$typeid);
$dlist->SetParameter("keyword",$keyword);
$dlist->SetSource($querystring);
include(dirname(__FILE__)."/templets/feedback_main.htm");
$dlist->Close();
$tl->Close();
?>