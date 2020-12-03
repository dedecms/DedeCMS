<?php
require_once(dirname(__FILE__)."/../include/common.inc.php");
require_once(dirname(__FILE__).'/../include/memberlogin.class.php');
$bid = (isset($bid) && is_numeric($bid)) ? $bid : 0;
$star = (isset($star) && is_numeric($star)) ? $star : 0;
if($bid == 0 || !in_array($star,array('1', '2', '3', '4', '5')) ) exit("Error:Wrong bid or stars,please check it!");
$cfg_ml = new MemberLogin();
$myip = GetIP();
if(!$cfg_ml->IsLogin())
{
	//如果用户没有登录
	echo "用户没有登录!";
}
$username = $cfg_ml->M_UserName;
$sql = "SELECT totalvotes, totalvalue, voteinfo, usedids FROM #@__story_bookstars WHERE bid='$bid' ";
$dsql->Execute('qr',$sql);
$numbers = $dsql->GetArray('qr');
$checkid = unserialize($numbers['usedids']);
$count = $numbers['totalvotes']; 
$currating = $numbers['totalvalue']; 
$sum = $star*2+$currating;
($sum==0 ? $added=0 : $added=$count+1);
$id_num = $username.'^'.$star;
((is_array($checkid)) ? array_push($checkid,$id_num) : $checkid=array($id_num));
$insertid=serialize($checkid);
//处理用户投票的信息
$checkinfo = unserialize($numbers['voteinfo']);
foreach ($checkinfo as $key => $value) {
  if($key == $star) $checkinfo[$key]= $value + 1;
}
$addinfo = serialize($checkinfo);

$sql = "SELECT usedids FROM #@__story_bookstars WHERE usedids LIKE '%".$cfg_ml->M_UserName."%' AND bid='".$bid."' ";
$dsql->Execute('vt',$sql);
$voted = $dsql->GetTotalRow('vt');
if(!$voted)
{
	if ($star >= 1 && $star <= 5) { 
		$update = "UPDATE #@__story_bookstars SET totalvotes='".$added."', totalvalue='".$sum."', voteinfo='".$addinfo."', usedids='".$insertid."' WHERE bid='$bid'";
		$result = $dsql->ExecuteNoneQuery($update);	
	} 
}
echo "<div style='width:120px'>成功评分!</div>";
?>
