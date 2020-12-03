<?
require_once(dirname(__FILE__)."/config.php");
if(!isset($nid)) $nid=0;
if(!isset($ids)) $ids="";
if(empty($ids))
{
	$dsql = new DedeSql(false);
	$inQuery = "Delete From #@__courl where nid='$nid'";
	$dsql->SetSql($inQuery);
	$dsql->ExecuteNoneQuery();
	$dsql->Close();
	ShowMsg("成功清空一个节点采集的内容!","co_main.php");
	exit();
}
else
{
	if(empty($_COOKIE["ENV_GOBACK_URL"])) $ENV_GOBACK_URL = "co_url.php";
	else $ENV_GOBACK_URL = $_COOKIE["ENV_GOBACK_URL"];
	$dsql = new DedeSql(false);
	$inQuery = "Delete From #@__courl where ";
	$idsSql = "";
	$ids = explode("`",$ids);
	foreach($ids as $id) $idsSql .= "or aid='$id' ";
	$idsSql = ereg_replace("^or ","",$idsSql);
	$dsql->SetSql($inQuery.$idsSql);
	$dsql->ExecuteNoneQuery();
	$dsql->Close();
	ShowMsg("成功删除指定的网址内容!",$ENV_GOBACK_URL);
	exit();
}
?>