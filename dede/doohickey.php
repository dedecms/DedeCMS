<?php 
require_once(dirname(__FILE__)."/config.php");
CheckPurview('plus_常用小技巧');
if(empty($ismake)) $dopost = "";
if($ismake=="1")
{
	 $dsql = new DedeSql(false);
	 $dtime = GetMkTime($sdate);
	 $query = "update #@__archives set ismake = $ismake";
	 $dsql->SetQuery($query);
	 $dsql->ExecuteNoneQuery();
	 $dsql->Close();
	 ShowMsg("批量替换文档生成状态为【静态页面】成功！","doohickey.php");
	 exit();
}
else if($ismake=="-1")
{
	 $dsql = new DedeSql(false);
	 $dtime = GetMkTime($sdate);
	 $query = "update #@__archives set ismake = $ismake";
	 $dsql->SetQuery($query);
	 $dsql->ExecuteNoneQuery();
	 $dsql->Close();
	 ShowMsg("批量替换文档生成状态为【动态页面】成功！","doohickey.php");
	 exit();
}

require_once(dirname(__FILE__)."/doohickey.html");

ClearAllLink();
?>