<?php
require_once(dirname(__FILE__)."/../include/config_base.php");
require_once(dirname(__FILE__)."/../include/inc_arclist_view.php");
if(!empty($typeid)) $tid = $typeid;
if(!isset($areaid)) $areaid = 0;
if(!isset($areaid2)) $areaid2 = 0;
if(!isset($sectorid)) $sectorid = 0;
if(!isset($sectorid2)) $sectorid2 = 0;
if(!isset($smalltypeid)) $smalltypeid = 0;
$tid    = intval($tid);
$areaid = intval($areaid);
$areaid2 = intval($areaid2);
$sectorid = intval($sectorid);
$sectorid2 = intval($sectorid2);
$smalltypeid = intval($smalltypeid);
$lv = new ListView($tid,0,$areaid,$areaid2,$sectorid,$sectorid2,$smalltypeid);
$lv->Display();
$lv->Close();
?>