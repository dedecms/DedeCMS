<?php 
require_once(dirname(__FILE__)."/config.php");
if($cfg_pp_isopen==1 && $cfg_pp_editsafeurl!=''){
	 header("Location:{$cfg_pp_editsafeurl}");
	 exit();
}
CheckRank(0,0);
require_once(dirname(__FILE__)."/templets/edit_pwd.htm");
?>