<?php 
require_once(dirname(__FILE__)."/config.php");
$action = (empty($action) ? '' : $action);

if($action==''){
  require_once(dirname(__FILE__)."/templets/makehtml_task.htm");
}else if($action=='save')
{
	
	
	
}


ClearAllLink();
?>