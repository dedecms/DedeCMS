<?php
require_once(dirname(__FILE__)."/config.php");
require_once DEDEINC.'/typelink.class.php';
require_once DEDEINC.'/dedetag.class.php';
$aid = isset($aid) && is_numeric($aid) ? $aid : 0;
$row = $dsql->GetOne("Select * From #@__freelist where aid='$aid' ");
$dtp = new DedeTagParse();
$dtp->SetNameSpace("dede","{","}");
$dtp->LoadSource("--".$row['listtag']."--");
$ctag = $dtp->GetTag('list');
require_once(DEDEADMIN."/templets/freelist_edit.htm");

?>