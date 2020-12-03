<?php 
require_once(dirname(__FILE__)."/config.php");
CheckPurview('temp_Test');
require_once(dirname(__FILE__)."/../include/inc_arcpart_view.php");
$partcode = stripslashes($partcode);
if(empty($typeid)) $typeid = 0;
if(empty($showsource)) $showsource = "";
if($typeid>0) $pv = new PartView($typeid);
else $pv = new PartView();
$pv->SetTemplet($partcode,"string");

header("Content-Type: text/html; charset={$cfg_ver_lang}");

if($showsource==""||$showsource=="yes"){
  echo "模板代码:";
  echo "<span style='color:red;'><pre>".htmlspecialchars($partcode)."</pre></span>";
  echo "结果:<hr size='1' width='100%'>";
}
$pv->Display();
$pv->Close();

ClearAllLink();
?>