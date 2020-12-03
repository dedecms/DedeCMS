<?php 
header("Content-Type: text/html; charset=utf-8");
header("Pragma:no-cache"); 
header("Cache-Control:no-cache"); 
header("Expires:0"); 
require(dirname(__FILE__)."/config.php");
CheckPurview('sys_Data');
if(empty($dopost)) $dopost = "";
$dsql = new DedeSql(false);
echo "<a href='#' onclick='javascript:HideObj(\"_mydatainfo\")'>[<u>关闭</u>]</a>\r\n<xmp>";
if($dopost=="viewinfo") //查看表结构
{
	if(empty($tablename)) echo "没有指定表名！";
	else{
		$dsql->SetQuery("SHOW CREATE TABLE ".$dsql->dbName.".".$tablename);
    $dsql->Execute();
    $row2 = $dsql->GetArray();
    $ctinfo = $row2[1];
    echo trim($ctinfo);
	}
	$dsql->Close();
	exit();
}
else if($dopost=="opimize") //优化表
{
	if(empty($tablename)) echo "没有指定表名！";
	else{
	  $dsql->ExecuteNoneQuery("OPTIMIZE TABLE '$tablename'");
	  $dsql->Close();
	  echo "执行优化表： $tablename  OK！";
  }
	exit();
}
else if($dopost=="repair") //修复表
{
	if(empty($tablename)) echo "没有指定表名！";
	else{
	  $rs = $dsql->ExecuteNoneQuery("REPAIR TABLE '$tablename'");
	  $dsql->Close();
	  echo "修复表： $tablename  OK！";
	}
	exit();
}
ClearAllLink();
echo "</xmp>";
?>