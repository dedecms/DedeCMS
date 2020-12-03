<?php 
//本计数器用于自由列表的浏览量计数
$__ONLYDB = true;
require_once(dirname(__FILE__)."/../include/config_base.php");

if(empty($aid)) $aid="0";

//仅第一页才计数，如果想每页都计数，把模板里的JS传递的 pageno 去除即可
if(empty($pageno)) $pageno = 1;
if($pageno!=1) exit();

$aid = ereg_replace("[^0-9]","",$aid);

$dsql = new DedeSql(false);
$dsql->ExecuteNoneQuery("Update #@__freelist set click=click+1 where aid='$aid'");
if(!empty($view)){
	$row = $dsql->GetOne("Select click From #@__freelist where aid='$aid'");
	echo "document.write('".$row[0]."');\r\n";
}
$dsql->Close();
exit();

/*-----------------------------------
如果想显示浏览次数,请增加view参数,即把下面ＪＳ调用放到文档模板适当位置 
<script src="{dede:field name='phpurl'/}/count-freelist.php?view=yes&aid={dede:field name='aid'/}&pageno={dede:pageno/}" language="javascript"></script>
普通计数器为
<script src="{dede:field name='phpurl'/}/count-freelist.php?aid={dede:field name='aid'/}&pageno={dede:pageno/}" language="javascript"></script>
----------------------------------*/
?>