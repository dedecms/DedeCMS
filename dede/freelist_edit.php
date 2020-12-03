<?php 
require_once(dirname(__FILE__)."/config.php");
require_once(dirname(__FILE__)."/../include/inc_typelink.php");
require_once(dirname(__FILE__)."/../include/pub_dedetag.php");
$dsql = new DedeSql(false);
$aid = ereg_replace("[^0-9]","",$aid);
$row = $dsql->GetOne("Select * From #@__freelist where aid='$aid' ");
$dtp = new DedeTagParse();
$dtp->SetNameSpace("dede","{","}");
$dtp->LoadSource("--".$row['listtag']."--");
$ctag = $dtp->GetTag('list');

require_once(dirname(__FILE__)."/templets/freelist_edit.htm");

ClearAllLink();
?>