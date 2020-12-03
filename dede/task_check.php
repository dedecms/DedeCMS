<?php
//------------------------------------
//计划任务验证部份
//------------------------------------
$_checkPage = true;
require_once(dirname(__FILE__)."/task_config.php");
$vars = '';
$dsql = new DedeSql(false);
$row = $dsql->GetOne("Select * From `#@__task`");
if(!is_array($row)||$row['usermtools']!=0||trim($row['rmpwd'])=='')
{
	echo '-1';
	$dsql->Close();
	exit();
}
//检测客户证书是否正确
if( empty($sgcode) || $sgcode != md5(trim($row['rmpwd']).$cfg_cookie_encode))
{
	echo '0';
	$dsql->Close();
	exit();
}
//证书正确时返回 1 ，并把客户证书生成验证文件
else
{
	$rmcertip = GetIP();
	$sgvalue = md5(trim($row['rmpwd']).$cfg_cookie_encode);
	$dsql->Close();
	$fp = fopen($cfg_basedir.'/data/rmcert.php','w') or die("-2");
	fwrite($fp,'<'.'?php'."\r\n  \$cfg_rmcert_value='{$sgvalue}';\r\n  \$cfg_rmcert_ip='{$rmcertip}';\r\n ?".'>');
	fclose($fp);
	echo '1';
	exit();
}
?>