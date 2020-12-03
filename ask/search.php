<?php

/**
 * Enter description here...
 *
 * @author Administrator
 * @package defaultPackage
 * @rcsfile 	$RCSfile: search.php,v $
 * @revision 	$Revision: 1.1 $
 * @date 	$Date: 2009/08/04 04:06:21 $
 */

require_once dirname(__FILE__).'/include/common.inc.php';
require_once DEDEINC.'/datalistcp.class.php';

$q = addslashes(ereg_replace("[\"\r\n\t\*\?\(\)\$%'><]"," ",stripslashes(trim($q))));

if($q=='' || strlen($q) < 3 || strlen($q) > 30)
{
	ShowMsg("关键字长度必须要3-30字节之间！","-1");
	exit();
}

$query = "select id, tid, tidname, tid2, tid2name, uid, title, reward, dateline, status, replies from #@__ask where title like '%$q%'";
$dlist = new DataListCP();
$dlist->pageSize = 20;

$dlist->SetParameter("q",$q);
$dlist->SetTemplate(DEDEASK.'template/default/search.htm');
$dlist->SetSource($query);

//快到期问题
$query = "select id, tid, tidname, tid2, tid2name, title from `#@__ask` where status='0' order by expiredtime asc, dateline desc limit 10";
$dsql->setquery($query);
$dsql->execute();
$expiredasks = array();
while($row = $dsql->getarray())
{
	if(strlen($row['title']) > 24)
	{
		$row['title'] = cn_substr($row['title'],24).'...';
	}
	$expiredasks[] = $row;
}
$dlist->Display();
?>