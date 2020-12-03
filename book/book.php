<?php

/**
 * Enter description here...
 *
 * @author Administrator
 * @package defaultPackage
 * @rcsfile 	$RCSfile: book.php,v $
 * @revision 	$Revision: 1.1 $
 * @date 	$Date: 2009/08/04 04:06:24 $
 */

require_once(dirname(__FILE__)."/../include/common.inc.php");
require_once(dirname(__FILE__).'/include/story.view.class.php');
$bid = (empty($bid) ? 0 : intval($bid));
if($bid==0)
{
	ParamError();
}
$bv = new BookView($bid,'book');
$ischeck = $bv->Fields['ischeck'];
if($ischeck == 0)
{
	require_once(DEDEINC."/../include/memberlogin.class.php");
	$ml = new MemberLogin();
	if($bv->Fields['mid'] != $ml->M_ID)
	{
		showmsg('图书未经审核', $cfg_mainsite.$cfg_cmspath.'/book');
		exit();
	}
}
$bv->Display();
$bv->Close();
?>