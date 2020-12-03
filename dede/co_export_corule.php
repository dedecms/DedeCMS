<?php 
require(dirname(__FILE__)."/config.php");
CheckPurview('co_EditNote');
$nid = ereg_replace("[^0-9]","",$nid);
if(empty($nid)){
   ShowMsg("参数无效!","-1");
   exit();
}
$dsql = new DedeSql(false);
$row = $dsql->GetOne("Select * From #@__conote where nid='$nid'");
$dsql->Close();
require_once(dirname(__FILE__)."/../include/pub_oxwindow.php");
$wintitle = "导出采集规则";
$wecome_info = "<a href='co_main.php'><u>采集节点管理</u></a>::导出采集规则";
$win = new OxWindow();
$win->Init();
$win->AddTitle("以下为规则 [{$row['gathername']}] 的文本配置，你可以共享给你的朋友：");
$winform = $win->GetWindow("hand","<xmp style='color:#333333;background-color:#ffffff'>".$row['noteinfo']."</xmp>");
$win->Display();

ClearAllLink();
?>