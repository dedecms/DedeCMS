<?php
require_once(dirname(__FILE__)."/../config.php");

$sql = "
DROP TABLE IF EXISTS `#@__story_books`;
DROP TABLE IF EXISTS `#@__story_catalog`;
DROP TABLE IF EXISTS `#@__story_chapter`;
DROP TABLE IF EXISTS `#@__story_content`;
DROP TABLE IF EXISTS `#@__story_viphistory`;

delete from `#@__sysconfig` where varname='cfg_book_freenum' limit 1;
delete from `#@__sysconfig` where varname='cfg_book_pay' limit 1;
delete from `#@__sysconfig` where varname='cfg_book_money' limit 1;
delete from `#@__sysconfig` where varname='cfg_book_freerank' limit 1;
delete from `#@__sysconfig` where varname='cfg_book_ifcheck' limit 1;

";

$db = new DedeSql(false);
$sqls = explode(';', $sql);
foreach($sqls as $sql){
	$db->executenonequery($sql);
}
$db->Close();

//删除管理菜单
$menuold = '';
$menufile = DEDEADMIN.'/inc/inc_menu.php';
$fp = fopen($menufile,'r') Or die("File： {$menufile} not found!");
while(!feof($fp)){ $menuold .= fread($fp, 8192); }
fclose($fp);

$menunew = preg_replace("/#story_menu_start#(.*)#story_menu_end#/is",'',$menuold);

$fp = fopen($menufile,"w");
fwrite($fp,$menunew);
fclose($fp);

//个人会员菜单
$membermenuold = '';
$membermenufile = DEDEADMIN.'/../member/templets/menu.php';
$fp = fopen($membermenufile,'r') Or die("File： {$membermenufile} not found!");
while(!feof($fp)) $membermenuold .= fread($fp, 8192);
fclose($fp);

$membermenunew = preg_replace("/<!--#story_menu_start#-->(.*)<!--#story_menu_end#-->/is",'',$membermenuold);

$fp = fopen($membermenufile,'w');
fwrite($fp,$membermenunew);
fclose($fp);

$rflwft = "
<script language='javascript'>
<!--
if(window.navigator.userAgent.indexOf('MSIE')>=1){
	top.document.frames.menu.location = '../index_menu.php?c=6';
}else{
	top.document.getElementById('menu').src = '../index_menu.php?c=6';
}
-->
</script>
";

echo $rflwft;
showmsg('模块卸载完成','../module_main.php');
?>