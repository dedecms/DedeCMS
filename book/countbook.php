<?php

/**
 * Enter description here...
 *
 * @author Administrator
 * @package defaultPackage
 * @rcsfile 	$RCSfile: countbook.php,v $
 * @revision 	$Revision: 1.1 $
 * @date 	$Date: 2009/08/04 04:06:24 $
 */

$__ONLYDB = true;
require_once(dirname(__FILE__)."/../include/common.inc.php");
$id = intval($aid);
$id = ereg_replace("[^0-9]","",$id);
$dsql->ExecuteNoneQuery("Update #@__story_books set click=click+1 where id='$id'");
if(!empty($view))
{
	$row = $dsql->GetOne("Select click From #@__story_books  where id='$id'");
	echo "document.write('".$row[0]."');\r\n";
}
exit();
//如果想显示点击次数,请增加view参数,即把下面ＪＳ调用放到文档模板适当位置
/*<script src="{dede:field name='phpurl'/}/countbook.php?view=yes&aid={dede:field name='id'/}" language="javascript"></script>
//普通计数器为
//<script src="{dede:field name='phpurl'/}/countbook.php?aid={dede:field name='id'/}" language="javascript"></script>
*/
?>