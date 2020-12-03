<?php 
require_once(dirname(__FILE__)."/config.php");
CheckRank(0,0);
$svali = GetCkVdValue();

if(strtolower($vdcode)!=$svali || $svali==""){
  ShowMsg("验证码错误！","-1");
  exit();
}

$cardid = ereg_replace("[^0-9A-Za-z-]","",$cardid);
if(empty($cardid)){
	ShowMsg("卡号为空！","-1");
  exit();
}

$dsql = new DedeSql(false);

$row = $dsql->GetOne("Select * From #@__moneycard_record where cardid='$cardid' ");

if(!is_array($row)){
	ShowMsg("卡号错误：不存在此卡号！","-1");
	$dsql->Close();
  exit();
}

if($row['isexp']==-1){
	ShowMsg("此卡号已经失效，不能再次使用！","-1");
	$dsql->Close();
  exit();
}

$hasMoney = $row['num'];

$dsql->ExecuteNoneQuery("update #@__moneycard_record set uid='".$cfg_ml->M_ID."',isexp='-1',utime='".time()."' where cardid='$cardid' ");

$dsql->ExecuteNoneQuery("update #@__member set money=money+$hasMoney where ID='".$cfg_ml->M_ID."'");

ShowMsg("充值成功，你本次增加的金币为：{$hasMoney} 个！","control.php");
$dsql->Close();
exit();

?>