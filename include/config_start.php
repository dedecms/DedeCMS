<?
$registerGlobals = @ini_get("register_globals");
$isUrlOpen = @ini_get("allow_url_fopen");
$isMagic = @ini_get("magic_quotes_gpc");
if(!$isMagic) require_once(dirname(__FILE__)."/config_rglobals_magic.php");
if($isMagic && !$registerGlobals) require_once(dirname(__FILE__)."/config_rglobals.php");

//Session±£´æÂ·¾¶
$sessSavePath = dirname(__FILE__)."/sessions/";
if(is_writeable($sessSavePath) && is_readable($sessSavePath)){ session_save_path($sessSavePath); }

?>