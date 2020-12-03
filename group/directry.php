<?php
/**
 * Enter description here...
 *
 * @author Administrator
 * @package defaultPackage
 * @version  $Id: directry.php,v 1.1 2009/08/04 04:07:29 blt Exp $
 */
require_once(dirname(__FILE__)."/config.php");

//目录树列表文件
$title = "所有分类";
$db->SetQuery("SELECT * FROM #@__store_groups WHERE tops=0 AND nums>0 ORDER BY nums DESC");
$db->Execute();
$stores = array();
while($rs = $db->GetArray())
{
	array_push ($stores,$rs);
}
require_once(DEDEGROUP."/templets/directry.htm");

?>