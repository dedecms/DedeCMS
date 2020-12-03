<?php 
//系统设置为维护状态可访问
$cfg_IsCanView = true;
$__ONLYDB = true;
require_once(dirname(__FILE__)."/../include/config_base.php");

if(empty($aid)) $aid="0";
$aid = ereg_replace("[^0-9]","",$aid);
if(empty($mid)) $mid="0";
$mid = ereg_replace("[^0-9]","",$mid);

$dsql = new DedeSql(-100);

//获取主表，并更新
$row = $dsql->GetOne("Select c.maintable From `#@__full_search` i left join #@__channeltype c on c.ID = i.channelid where i.aid = '$aid'; ",MYSQL_NUM);
if(empty($row[0])) $row[0] = '#@__archives';
$dsql->ExecuteNoneQuery("Update `{$row[0]}` set click=click+1 where ID='$aid'");
$dsql->ExecuteNoneQuery("Update `#@__full_search` set click=click+1 where aid='$aid'");

//更新会员文档浏览数
if(!empty($mid)){
	$dsql->ExecuteNoneQuery("Update `#@__member` set pageshow=pageshow+1 where ID='$mid'");
}

//获得计数器值
if(!empty($view)){
	$row = $dsql->GetOne("Select click From `{$row[0]}` where ID='$aid'",MYSQL_NUM);
	echo "document.write('".$row[0]."');\r\n";
}

$dsql->Close();
exit();

/*----------------
计数器调用说明：

如果想显示点击次数,请增加view参数,即把下面ＪＳ调用放到文档模板适当位置 
<script src="{dede:field name='phpurl'/}/count.php?view=yes&aid={dede:field name='ID'/}&mid={dede:field name='memberID'/}" language="javascript"></script>

普通计数器为
<script src="{dede:field name='phpurl'/}/count.php?aid={dede:field name='ID'/}&mid={dede:field name='memberID'/}" language="javascript"></script>
----------------*/
?>