<?php
if(!file_exists(dirname(__FILE__) . "/data/common.inc.php"))
{
    header("Location:install/index.php");
    exit();
}
require_once (dirname(__FILE__) . "/include/common.inc.php");
require_once DEDEINC."/arc.partview.class.php";
$GLOBALS['_arclistEnv'] = 'index';
$row = $dsql->GetOne("Select * From `#@__homepageset`");
$row['templet'] = MfTemplet($row['templet']);
$pv = new PartView();
$pv->SetTemplet($cfg_basedir . $cfg_templets_dir . "/" . $row['templet']);
$pv->Display();

?>