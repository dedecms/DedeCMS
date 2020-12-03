<?php
/**
 * Enter description here...
 *
 * @author Administrator
 * @package defaultPackage
 * @version  $Id: members.php,v 1.1 2009/08/04 04:07:29 blt Exp $
 */
require_once(dirname(__FILE__)."/config.php");
include_once DEDEGROUP."/global.inc.php";
include_once DEDEINC.'/datalistcp.class.php';

$sql = "SELECT U.posts,U.replies,U.jointime, M.uname,M.face,M.userid FROM #@__group_user U LEFT JOIN #@__member M ON U.uid=M.mid WHERE U.gid='$id' AND U.isjoin=1 AND (U.uid NOT IN(".$_GROUPS['admin_ids'].")) ORDER BY U.id DESC";

$dl = new DataListCP();
$dl->pageSize = 18;    //设定每页显示记录数
$dl->SetParameter('id',$id);  //设定get字符串的变量

//这两句的顺序不能更换
$dl->SetTemplate(GROUP_TPL.'/members.html'); //载入模板
$dl->SetSource($sql); //设定查询SQL
$dl->Display();
?>