<?
require_once(dirname(__FILE__)."/config.php");
require_once(dirname(__FILE__)."/templets/login.htm");
if(isset($dsql) && is_object($dsql)) $dsql->Close();
?>