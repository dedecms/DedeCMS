<?
require_once(dirname(__FILE__)."/config.php");
setcookie("ENV_GOBACK_URL",$dedeNowurl,time()+3600,"/");
SetPageRank(10);
$dsql = new DedeSql(false);
if($sta==1)
{
	$query1 = "update #@__keywords set sta=0 where aid='$aid' ";
	$dsql->SetQuery($query1);
	$dsql->ExecuteNoneQuery();
	$query2 = "update #@__archives set keywords = Replace(keywords,'$keyword ','') where channel=1";
  $dsql->SetQuery($query2);
	$dsql->ExecuteNoneQuery();
}
else
{
	$query1 = "update #@__keywords set sta=1 where aid='$aid' ";
	$dsql->SetQuery($query1);
	$dsql->ExecuteNoneQuery();
}

$dsql->Close();

ShowMsg("成功更改关键字属性！","-1",0,1000);

?>