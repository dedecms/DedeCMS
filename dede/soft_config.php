<?php 
require_once(dirname(__FILE__)."/config.php");
CheckPurview('sys_SoftConfig');
if(empty($dopost)) $dopost = "";
//保存
$dsql = new DedeSql(false);
$upok = '';
if($dopost=="save")
{
   $query = "UPDATE `#@__softconfig` SET downtype = '$downtype' , showlocal = '$showlocal', 
   gotojump='$gotojump' , ismoresite = '$ismoresite',sites = '$sites'";
   $dsql->SetQuery($query);
   $dsql->ExecuteNoneQuery();
   $upok = "<font color='red'>成功保存更改！</font>";
}
//读取参数
$row = $dsql->GetOne("select * From #@__softconfig");
if(!is_array($row)){
	$dsql->ExecuteNoneQuery("INSERT INTO `#@__softconfig` ( `downtype` , `ismoresite` ,`showlocal` , `gotojump` , `sites` ) VALUES ('0', '0','0' , '0', '');");
	$row['downtype']=1;
	$row['ismoresite']=0;
	$row['sites']="";
	$row['gotojump']=0;
}

require_once(dirname(__FILE__)."/templets/soft_config.htm");

ClearAllLink();
?>