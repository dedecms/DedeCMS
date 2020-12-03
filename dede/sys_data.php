<?php
require_once(dirname(__FILE__)."/config.php");
CheckPurview('sys_Data');
//获取系统存在的表信息
$otherTables = Array();
$dedeSysTables = Array();
$channelTables = Array();
$dsql = new DedeSql(false);
$dsql->SetQuery("Select addtable From #@__channeltype");
$dsql->Execute();
while($row = $dsql->GetObject()){
	$channelTables[] = $row->addtable;
}
$dsql->SetQuery("Show Tables");
$dsql->Execute('t');
while($row = $dsql->GetArray('t')){

	if(ereg("^{$cfg_dbprefix}",$row[0])||in_array($row[0],$channelTables))
	{  $dedeSysTables[] = $row[0];  }
	else{ $otherTables[] = $row[0]; }
}

function TjCount($tbname,$dsql){
   $row = $dsql->GetOne("Select count(*) as dd From $tbname");
   return $row['dd'];
}

$mysql_version = $dsql->GetVersion();
$mysql_versions = explode(".",trim($mysql_version));
$mysql_version = $mysql_versions[0].".".$mysql_versions[1];

require_once(dirname(__FILE__)."/templets/sys_data.htm");

ClearAllLink();

?>