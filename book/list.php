<?php

/**
 * Enter description here...
 *
 * @author Administrator
 * @package defaultPackage
 * @rcsfile 	$RCSfile: list.php,v $
 * @revision 	$Revision: 1.1 $
 * @date 	$Date: 2009/08/04 04:06:24 $
 */

require_once(dirname(__FILE__)."/../include/common.inc.php");
require_once(dirname(__FILE__).'./include/story.view.class.php');
$id = intval($id);
if(empty($id))
{
	ParamError();
}
$bv = new BookView($id,'catalog');
$bv->Display();
$bv->Close();
?>