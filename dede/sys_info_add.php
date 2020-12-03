<?php 
require_once(dirname(__FILE__)."/config.php");
CheckPurview('sys_Edit');

$varname = $_POST['varname'];
if(!eregi('cfg_',$varname)){
	ShowMsg("变量名称必须以 cfg_ 开头","-1");
	exit();
}

if($vartype=='bool' && ($varvalue!='Y' && $varvalue!='N')){
	ShowMsg("布尔变量值必须为'Y'或'N'!","-1");
	exit();
}
$varvalue = htmlspecialchars($varvalue);
$dsql = new DedeSql(false);

$row = $dsql->GetOne("Select * From #@__sysconfig where varname like '$varname' ");
if(is_array($row)){
	 ShowMsg("该变量名称已经存在!","-1");
	 $dsql->Close();
	 exit();
}

$row = $dsql->GetOne("Select * From #@__sysconfig order by aid desc ");
$aid = $row['aid']+1;

$inquery = "INSERT INTO `#@__sysconfig`(`aid`,`varname`,`info`,`value`,`type`,`group`) 
VALUES ('$aid','$varname','$varmsg','$varvalue','$vartype','$vargroup')";

$rs = $dsql->ExecuteNoneQuery($inquery);

if(!$rs){
	 $dsql->Close();
	 ShowMsg("新增变量失败，可能有非法字符！","sys_info.php?gp=$vargroup");
	 exit();
}

$configfile = dirname(__FILE__)."/../include/config_hand.php";
$configfile_bak = dirname(__FILE__)."/../include/config_hand_bak.php";

if(!is_writeable($configfile)){
	$dsql->Close();
	ShowMsg("成功保存变量，但由于 $configfile 无法写入，因此不能更新配置文件！","sys_info.php?gp=$vargroup");
	exit();
}else{
	$dsql->SetQuery("Select varname,value From #@__sysconfig order by aid asc");
	$dsql->Execute();
	if($dsql->GetTotalRow()<=0){
		$dsql->Close();
		ShowMsg("成功保存变量但从数据库读取所有数据时失败，无法更新配置文件！","sys_info.php?gp=$vargroup");
	  exit();
	}
	copy($configfile,$configfile_bak);
	$fp = fopen($configfile,"w");
	fwrite($fp,"<"."?php\r\n");
  while($row = $dsql->GetArray()){
  	fwrite($fp,"\${$row['varname']} = '".str_replace("'","\\'",$row['value'])."';\r\n");
  }
  fwrite($fp,"?".">");
	fclose($fp);
	$dsql->Close();
	ShowMsg("成功保存变量并更新配置文件！","sys_info.php?gp=$vargroup");
	exit();
}

ClearAllLink();
?>