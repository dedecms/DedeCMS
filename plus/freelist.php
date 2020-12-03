<?php 
require_once(dirname(__FILE__)."/../include/inc_freelist_view.php");
$tid = intval($lid);
$fl = new FreeList($lid);
$fl->Display();
$fl->Close();
?>