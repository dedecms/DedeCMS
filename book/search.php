<?php

/**
 * Enter description here...
 *
 * @author Administrator
 * @package defaultPackage
 * @rcsfile 	$RCSfile: search.php,v $
 * @revision 	$Revision: 1.1 $
 * @date 	$Date: 2009/08/04 04:06:24 $
 */

require_once(dirname(__FILE__)."/../include/common.inc.php");
require_once(dirname(__FILE__).'/include/story.view.class.php');
$kws = Array();
if(!empty($id))
{
	$kws['id'] = intval($id);
}
if(!empty($keyword))
{
	$kws['keyword'] = html2text($keyword);
}
if(!empty($author))
{
	$kws['author'] = html2text($author);
}
if(count($kws)==0)
{
	ParamError();
}
$bv = new BookView(0,'search',$kws);
$bv->Display();
$bv->Close();
?>