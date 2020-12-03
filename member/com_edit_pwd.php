<?php
require_once(dirname(__FILE__)."/config.php");
require_once(dirname(__FILE__)."/com_config.php");

if($cfg_pp_isopen==1 && $cfg_pp_editsafeurl!=''){
	 header("Location:{$cfg_pp_editsafeurl}");
	 exit();
}
CheckRank(0,0);
require_once(dirname(__FILE__)."/templets/com_edit_pwd.htm");
?>