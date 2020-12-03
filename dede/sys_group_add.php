<?php 
require_once(dirname(__FILE__)."/config.php");
CheckPurview('sys_Group');
if(!empty($dopost)){
   $dsql = new DedeSql(false);
   $row = $dsql->GetOne("Select * From #@__admintype where rank='".$rankid."'");
   if(is_array($row)){
   	  ShowMsg("你所创建的组别的级别值已存在，不允许重复!","-1");
	    $dsql->Close();
	    exit();
   }
   $AllPurviews = "";
   if(is_array($purviews)){
   	 foreach($purviews as $pur){
   	 	 $AllPurviews = $pur.' ';
   	 }
   	 $AllPurviews = trim($AllPurviews);
   }
   $dsql->ExecuteNoneQuery("INSERT INTO #@__admintype(rank,typename,system,purviews) VALUES ('$rankid','$groupname', 0, '$AllPurviews');");
   ShowMsg("成功创建一个新的用户组!","sys_group.php");
	 $dsql->Close();
	 exit();
}


require_once(dirname(__FILE__)."/templets/sys_group_add.htm");

ClearAllLink();
?>