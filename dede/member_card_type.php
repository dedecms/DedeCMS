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
   for(;$startID<=$endID;$startID++){
   	  $query = '';
   	  $tid = ${'ID_'.$startID};
   	  $pname =   ${'pname_'.$startID};
   	  $money =    ${'money_'.$startID};
   	  $num =   ${'num_'.$startID};
   	  if(isset(${'check_'.$startID})){
   	  	if($pname!=''){
   	  		$query = "update #@__moneycard_type set pname='$pname',money='$money',num='$num' where tid='$tid'";
   	  		$dsql->ExecuteNoneQuery($query);
   	  		$query = "update #@__moneycard_record set money='$money',num='$num' where ctid='$tid' ; ";
   	  		$dsql->ExecuteNoneQuery($query);
   	  	}
   	  }
   	  else{
   	  	$query = "Delete From #@__moneycard_type where tid='$tid' ";
   	  	$dsql->ExecuteNoneQuery($query);
   	  	$query = "Delete From #@__moneycard_record where ctid='$tid' And isexp<>-1 ; ";
   	  	$dsql->ExecuteNoneQuery($query);
   	  }
   }
   //增加新记录
   if(isset($check_new) && $pname_new!=''){
   	 	$query = "Insert Into #@__moneycard_type(num,pname,money) Values('{$num_new}','{$pname_new}','{$money_new}');";
		  $dsql->ExecuteNoneQuery($query);
   }
   header("Content-Type: text/html; charset={$cfg_ver_lang}");
   echo "<script> alert('成功更新点卡产品分类表！'); </script>";
}


require_once(dirname(__FILE__)."/templets/member_card_type.htm");

ClearAllLink();

?>