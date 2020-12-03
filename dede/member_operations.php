<?php
require_once(dirname(__FILE__)."/config.php");
CheckPurview('member_Operations');
setcookie("ENV_GOBACK_URL",$dedeNowurl,time()+3600,"/");
require_once(DEDEINC.'/datalistcp.class.php');
if(empty($buyid))
{
	$buyid = '';
}
$addsql = " where buyid like '%$buyid%' ";
if(isset($sta))
{
	$addsql .= " And sta='$sta' ";
}
$sql = "Select * From #@__member_operation $addsql order by aid desc";
$dlist = new DataListCP();

//设定每页显示记录数（默认25条）
$dlist->pageSize = 25;
$dlist->SetParameter("buyid",$buyid);
if(isset($sta))
{
	$dlist->SetParameter("sta",$sta);
}
$dlist->dsql->SetQuery("Select * From #@__moneycard_type ");
$dlist->dsql->Execute('ts');
while($rw = $dlist->dsql->GetArray('ts'))
{
	$TypeNames[$rw['tid']] = $rw['pname'];
}
$tplfile = DEDEADMIN."/templets/member_operations.htm";

//这两句的顺序不能更换
$dlist->SetTemplate($tplfile);      //载入模板
$dlist->SetSource($sql);            //设定查询SQL
$dlist->Display();                  //显示

function GetMemberID($mid)
{
	global $dsql;
	if($mid==0)
	{
		return '0';
	}
	$row = $dsql->GetOne("Select userid From #@__member where mid='$mid' ");
	if(is_array($row))
	{
		return "<a href='member_view.php?mid={$mid}'>".$row['userid']."</a>";
	}
	else
	{
		return '0';
	}
}

function GetPType($tname)
{
	return $tname=='card' ? '点数卡' : '会员升级';
}

function GetSta($sta)
{
	if($sta==0)
	{
		return '未付款';
	}
	else if($sta==1)
	{
		return '已付款';
	}
	else
	{
		return '已完成';
	}
}

?>