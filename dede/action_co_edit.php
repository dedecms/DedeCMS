<?
require_once(dirname(__FILE__)."/config.php");
if(!isset($isenum)) $isenum=0;
if($isenum==1) $typeid="0";
$itemconfig = "
{dede:comments}
{!-- 节点基本信息 --}
{/dede:comments}

{dede:item name=\\'$notename\\' typeid=\\'$typeid\\'
  imgurl=\\'$imgurl\\' imgdir=\\'$imgdir\\' language=\\'$language\\'}
{/dede:item}

$otherconfig
";
$inQuery = "
Update #@__conote set typeid='$typeid',gathername='$notename',language='$language',noteinfo='$itemconfig' 
Where nid='$nid';
";
$dsql = new DedeSql(false);
$dsql->SetSql($inQuery);
if($dsql->ExecuteNoneQuery())
{
	$dsql->Close();
	ShowMsg("成功更改一个节点!","co_main.php");
	exit();
}
else
{
	$dsql->Close();
	ShowMsg("更改节点失败,请检查原因!","-1");
	exit();
}
?>