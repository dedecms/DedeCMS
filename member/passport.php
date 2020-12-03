<?php 
require_once(dirname(__FILE__)."/../include/config_base.php");
require_once(dirname(__FILE__)."/../include/inc_memberlogin.php");
require_once(dirname(__FILE__)."/../include/inc_dedepassport_config.php");
if(empty($cfg_pp_index)) $cfg_pp_index = $cfg_memberurl."/index.php";
if(!eregi('^http',$cfg_pp_index)) $cfg_pp_index = 'http://'.$cfg_pp_index;



?>