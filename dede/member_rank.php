<?php 
require_once(dirname(__FILE__)."/config.php");
CheckPurview('member_Type');
if(empty($dopost)) $dopost = "";
$dsql = new DedeSql(false);
//保存更改
//--------------------
if($dopost=="save")
{
   $startID = 1;
   $endID = $idend;
   for(;$startID<=$endID;$startID++)
   {
   	  $query = "";
   	  $ID = ${"ID_".$startID};
   	  $name = ${"name_".$startID};
   	  $rank = ${"rank_".$startID};
   	  $money = ${"money_".$startID};
   	  if(isset(${"check_".$startID})){
   	  	if($rank>0) $query = "update #@__arcrank set membername='$name',money='$money',rank='$rank' where ID='$ID'";
   	  }
   	  else{
   	  	$query = "Delete From #@__arcrank where ID='$ID' And rank<>10";
   	  }
   	  
   	  if($query!=""){
   	  	$dsql->SetQuery($query);
   	  	$dsql->ExecuteNoneQuery();
   	  } 
   }
   if(isset($check_new))
   {
   	 if($rank_new>0 && $name_new!="" && $money_new!=""){
   	 	 $dsql->SetQuery("Insert Into #@__arcrank(rank,membername,adminrank,money) Values('$rank_new','$name_new','5','$money_new')");
   	   $dsql->ExecuteNoneQuery();
   	 }
   }
   header("Content-Type: text/html; charset={$cfg_ver_lang}");
   echo "<script> alert('成功更新会员等级表！'); </script>";
}

require_once(dirname(__FILE__)."/templets/member_rank.htm");

ClearAllLink();
?>