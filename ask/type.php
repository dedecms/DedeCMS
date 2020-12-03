<?php

/**
 * Enter description here...
 *
 * @author Administrator
 * @package defaultPackage
 * @rcsfile 	$RCSfile: type.php,v $
 * @revision 	$Revision: 1.1 $
 * @date 	$Date: 2009/08/04 04:06:21 $
 */

require_once dirname(__FILE__).'/include/common.inc.php';
require_once dirname(__FILE__).'/include/asktype.inc.php';
$dtp = new DedeTemplate();
$dtp->LoadTemplate(DEDEASK.'template/default/type.htm');
$dtp->Display();
?>