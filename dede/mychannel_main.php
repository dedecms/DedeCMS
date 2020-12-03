<?php 
require_once(dirname(__FILE__)."/config.php");
CheckPurview('c_List');
require_once(dirname(__FILE__)."/../include/pub_datalist.php");
require_once(dirname(__FILE__)."/../include/inc_functions.php");
setcookie("ENV_GOBACK_URL",$dedeNowurl,time()+3600,"/");

function GetSta($sta,$ID)
{
	if($sta==1) return "启用  &gt; <a href='mychannel_edit.php?dopost=hide&ID=$ID'><u>禁用</u></a>";
	else return "<font color='red'>禁用</font> &gt; <a href='mychannel_edit.php?dopost=show&ID=$ID'><u>启用</u></a>";
}

function IsSystem($s)
{
	if($s==1) return "系统模型";
	else return "自动模型";
}

$sql = "Select ID,nid,typename,addtable,mancon,isshow,issystem From #@__channeltype order by ID";

$dlist = new DataList();
$dlist->Init();
$dlist->SetSource($sql);
$dlist->SetTemplet(dirname(__FILE__)."/templets/mychannel_main.htm");
$dlist->display();
$dlist->Close();

ClearAllLink();
?>