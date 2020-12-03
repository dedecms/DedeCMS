<?php
require_once(dirname(__FILE__)."/../include/common.inc.php");
if(isset($arcID))
{
	$aid = $arcID;
}
$arcID = $aid = (isset($aid) && is_numeric($aid)) ? $aid : 0;
if($aid==0)
{
	exit();
}
$mid = (isset($mid) && is_numeric($mid)) ? $mid : 0;

//UpdateStat();
$dsql->ExecuteNoneQuery(" Update `#@__archives` set click=click+1 where id='$aid' ");
if(!empty($mid))
{
	$dsql->ExecuteNoneQuery(" Update `#@__member_tj` set pagecount=pagecount+1 where mid='$mid' ");
}
if(!empty($view))
{
	$row = $dsql->GetOne(" Select click From `#@__archives`  where id='$aid' ");
	if(is_array($row))
	{
		echo "document.write('".$row['click']."');\r\n";
	}
}
exit();
/*-----------
如果想显示点击次数,请增加view参数,即把下面ＪＳ调用放到文档模板适当位置
<script src="{dede:field name='phpurl'/}/count.php?view=yes&aid={dede:field name='id'/}&mid={dede:field name='mid'/}" language="javascript"></script>
普通计数器为
<script src="{dede:field name='phpurl'/}/count.php?aid={dede:field name='id'/}&mid={dede:field name='mid'/}" language="javascript"></script>
------------*/
?>