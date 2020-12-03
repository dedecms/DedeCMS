<?php

/**
 * Enter description here...
 *
 * @author Administrator
 * @package defaultPackage
 * @rcsfile 	$RCSfile: asktype.inc.php,v $
 * @revision 	$Revision: 1.1 $
 * @date 	$Date: 2009/08/04 04:06:21 $
 */

defined('DEDEASK') or exit('Request Error!');
//问答分类
$query = "select id, name, asknum, reid from `#@__asktype` order by disorder desc, id asc";
$dsql->Execute('me',$query);
$tids = $tid2s = $asktypes = array();
while($asktype = $dsql->getarray())
{
	if($asktype['reid'] == 0)
	{
		$tids[] = $asktype;
	}else
	{
		$tid2s[] = $asktype;
	}

}
foreach($tids as $tid)
{
	$asktypes[] = $tid;
	foreach($tid2s as $key => $tid2)
	{
		if($tid2['reid'] == $tid['id'])
		{
			$asktypes[] = $tid2;
			unset($tid2s[$key]);
		}
	}
}
?>