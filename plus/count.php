<?
require_once(dirname(__FILE__)."/../include/config_base.php");
if(empty($aid)) $aid="0";
$aid = ereg_replace("[^0-9]","",$aid);
$dsql = new DedeSql(false);
$dsql->SetQuery("Update #@__archives set click=click+1 where ID='$aid'");
$dsql->ExecuteNoneQuery();
if(!empty($view))
{
	$row = $dsql->GetOne("Select click From #@__archives  where ID='$aid'");
	echo "document.write('".$row[0]."');\r\n";
}
$dsql->Close();
exit();
//如果想显示点击次数,请增加view参数,
//即把 
//<script src="{dede:field name='phpurl'/}/count.php?view=yes&aid={dede:field name='ID'/}" language="javascript"></script>
//放到适当文档模板位置
?>