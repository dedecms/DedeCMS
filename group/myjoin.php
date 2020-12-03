<?php
/**
 * Enter description here...
 *
 * @author Administrator
 * @package defaultPackage
 * @version  $Id: myjoin.php,v 1.1 2009/08/04 04:07:30 blt Exp $
 */
require_once(dirname(__FILE__)."/system/config.php");
require_once(DEDEINC."/datalistcp.class.php");
$menutype = 'mydede';

$sql = "SELECT t.gid, t.subject, t.lastpost,t.tid,g.groupname FROM #@__group_threads AS t LEFT JOIN  #@__groups AS g ON g.groupid=t.gid WHERE t.authorid='".$cfg_ml->M_ID."' AND t.closed='0' ORDER BY t.lastpost DESC";

/*$sql = "SELECT t.gid, t.subject, g.groupname,t.lastpost,t.tid
FROM #@__group_threads AS t
LEFT JOIN #@__groups AS g ON g.groupid=t.gid
WHERE t.authorid='".$cfg_ml->M_ID."' AND t.closed='0'
GROUP BY t.gid
ORDER BY t.lastpost DESC";*/

//$sql = "SELECT gid, subject,(SELECT groupname FROM #@__groups WHERE groupid=t.gid) AS groupname,lastpost,tid FROM #@__group_threads AS t WHERE authorid='".$cfg_ml->M_ID."' AND closed='0' GROUP BY gid ORDER BY lastpost DESC";

$dl = new DataListCP();
$dl->pageSize = 20;

//这两句的顺序不能更换
$dl->SetTemplate(_SYSTEM_."/myjoin.htm");      //载入模板
$dl->SetSource($sql);            //设定查询SQL
$dl->Display();                  //显示

?>