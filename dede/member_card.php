<?php 
require_once(dirname(__FILE__)."/config.php");
require_once(dirname(__FILE__)."/../include/pub_datalist_dm.php");

function GetMemberID($dsql,$mid){
	if($mid==0) return '0';
	$row = $dsql->GetOne("Select userid From #@__member where ID='$mid' ");
	if(is_array($row)) return "<a href='member_view.php?ID={$mid}'>".$row['userid']."</a>";
	else return '0';
}

function GetSta($sta){
	if($sta==1) return '已售出';
	else if($sta==-1) return '已使用';
	else return '未使用';
}

$addsql = '';
if(isset($isexp)){
	$addsql = " where isexp='$isexp' ";
}

$sql = "Select * From #@__moneycard_record $addsql order by aid desc";
$dlist = new DataList();
$dlist->Init();

if(isset($isexp)){
	$dlist->SetParameter("isexp",$isexp);
}

$dlist->dsql->SetQuery("Select * From #@__moneycard_type ");
$dlist->dsql->Execute('ts');
while($rw = $dlist->dsql->GetArray('ts')){
	$TypeNames[$rw['tid']] = $rw['pname'];
}

$dlist->SetSource($sql);
include(dirname(__FILE__)."/templets/member_card.htm");
$dlist->Close();

ClearAllLink();
?>


