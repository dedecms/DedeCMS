<?php 
require_once(dirname(__FILE__)."/config.php");
if(empty($dopost)) $dopost = "";
CheckPurview('sys_Att');
$dsql = new DedeSql(false);

if($dopost=="save")
{
   $startID = 1;
   $endID = $idend;
   for(;$startID<=$endID;$startID++)
   {
   	  $query = "";
   	  $att = ${"att_".$startID};
   	  $attname = ${"attname_".$startID};
   	  if(isset(${"check_".$startID})){
   	  	$query = "update #@__arcatt set attname='$attname' where att='$att'";
   	  }
   	  else{
   	  	$query = "Delete From #@__arcatt where att='$att'";
   	  }
   	  if($query!=""){
   	  	$dsql->SetQuery($query);
   	  	$dsql->ExecuteNoneQuery();
   	  } 
   }
   if(isset($check_new))
   {
   	 if($att_new>0 && $attname_new!=""){
   	 	 $dsql->SetQuery("Insert Into #@__arcatt(att,attname) Values('{$att_new}','{$attname_new}')");
   	   $dsql->ExecuteNoneQuery();
   	 }
   }
   //header("Content-Type: text/html; charset={$cfg_ver_lang}");
   echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset={$cfg_ver_lang}\">\r\n";
   echo "<script> alert('成功更新自定文档义属性表！'); </script>";
}

require_once(dirname(__FILE__)."/templets/content_att.htm");

ClearAllLink();

?>
