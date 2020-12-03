<?php 
require(dirname(__FILE__)."/../include/inc_arcpart_view.php");
$aid = ereg_replace("[^0-9]","",$aid);
$pv = new PartView();
$row = $pv->dsql->GetOne("Select tagname From #@__mytag where aid='$aid'");
$myvalues = $pv->GetMyTag(0,$row['tagname'],"yes");
$pv->Close();
$myvalues = str_replace('"','\"',$myvalues);
$myvalues = str_replace("\r","\\r",$myvalues);
$myvalues = str_replace("\n","\\n",$myvalues);
echo "<!--\r\n";
echo "document.write(\"{$myvalues}\");\r\n";
echo "-->\r\n";
?>