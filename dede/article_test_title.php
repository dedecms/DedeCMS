<?php
require_once(dirname(__FILE__)."/config.php");
AjaxHead();
if(empty($t) || $cfg_check_title=='N')
{
	exit;
}
$row = $dsql->GetOne("Select id From `#@__archives` where title like '$t' ");
if(is_array($row))
{
	echo "提示：系统已经存在标题为 '<a href='../plus/view.php?aid={$row['id']}' style='color:red' target='_blank'><u>$t</u></a>' 的文档。[<a href='#' onclick='javascript:HideObj(\"_mytitle\")'>关闭</a>]";
}

?>