<?
require_once(dirname(__FILE__)."/config.php");
empty($_COOKIE['ENV_GOBACK_URL']) ? $ENV_GOBACK_URL = "content_list.php" : $ENV_GOBACK_URL=$_COOKIE['ENV_GOBACK_URL'];
SetPageRank(10);
$dsql = new DedeSql(false);
if($keyword!="")
{
	 $query = "update #@__archives set keywords = Replace(keywords,'$keyword ','') where channel=1";
   $dsql->SetQuery($query);
	 $dsql->ExecuteNoneQuery();
}
$dsql->SetQuery("Delete From #@__keywords where aid='$aid'");
$dsql->ExecuteNoneQuery();
$dsql->Close();
ShowMsg("成功删除一个关键字！",$ENV_GOBACK_URL);
?>