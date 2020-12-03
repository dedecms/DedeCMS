<?php
require_once(dirname(__FILE__)."/config.php");
AjaxHead();
if(empty($t)) echo '';
$dsql = new DedeSql(false);
$row = $dsql->GetOne("Select aid From #@__full_search where title like '$t' ");
$dsql->Close();
if(is_array($row)) echo "提示：系统已经存在标题为 '<a href='../plus/view.php?aid={$row['aid']}' style='color:red' target='_blank'><u>$t</u></a>' 的文档。[<a href='#' onclick='javascript:HideObj(\"_mytitle\")'>关闭</a>]";
else echo '';
?>