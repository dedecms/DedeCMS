<?php
require_once(dirname(__FILE__)."/../include/config_base.php");
$dsql = new DedeSql(false);
$row = $dsql->GetOne("Select ID From #@__arctype where channeltype=-2 And reID=0 order by sortrank asc");
$dsql->Close();
if(!is_array($row)){
	ShowMsg("分类信息模块没安装或尚未创建栏目!","-1");
}else{
	header("location:".$cfg_cmspath."/plus/list.php?tid=".$row['ID']);
}
?>