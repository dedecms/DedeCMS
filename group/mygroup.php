<?php
/**
 * Enter description here...
 *
 * @author Administrator
 * @package defaultPackage
 * @version  $Id: mygroup.php,v 1.1 2009/08/04 04:07:30 blt Exp $
 */
require_once(dirname(__FILE__)."/system/config.php");
require_once(DEDEINC."/datalistcp.class.php");
$menutype = 'mydede';
$sql = "SELECT * FROM #@__groups WHERE ishidden='0' AND uid='".$cfg_ml->M_ID."'  ORDER BY threads DESC,stime DESC";
$dl = new DataListCP();
$dl->pageSize = 20;
//这两句的顺序不能更换
$dl->SetTemplate(_SYSTEM_."/mygroup.htm");      //载入模板
$dl->SetSource($sql);            //设定查询SQL
$dl->Display();                  //显示
?>