<?php

/**
 * Enter description here...
 *
 * @author Administrator
 * @package defaultPackage
 * @rcsfile 	$RCSfile: myask.php,v $
 * @revision 	$Revision: 1.1 $
 * @date 	$Date: 2009/08/04 04:06:20 $
 */

require_once(dirname(__FILE__)."/../member/config.php");
require_once DEDEINC.'/datalistcp.class.php';
CheckRank(0,0);
$timestamp = time();
$myasks = array();
$query = "select id, tid, tidname, tid2, tid2name, uid, title, digest, reward, dateline, expiredtime, solvetime, status, replies from `#@__ask` where uid='{$cfg_ml->M_ID}' ";
$dlist = new DataListCP();
$dlist->pageSize = 20;
$dlist->SetTemplate(dirname(__FILE__).'/template/default/myask.htm');
$dlist->SetSource($query);
$dlist->Display();

?>