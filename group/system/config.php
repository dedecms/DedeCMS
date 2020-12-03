<?php
/**
 * Enter description here...
 *
 * @author Administrator
 * @package defaultPackage
 * @version  $Id: config.php,v 1.1 2009/08/04 04:07:37 blt Exp $
 */
require_once(dirname(__FILE__)."/../../member/config.php");
CheckRank(0,0);
define('_SYSTEM_', DEDEROOT.'/group/templets/system');
?>