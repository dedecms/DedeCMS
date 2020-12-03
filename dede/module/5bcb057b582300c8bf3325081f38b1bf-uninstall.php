<?php
require_once(dirname(__FILE__)."/../config.php");

$sql = "
DROP TABLE IF EXISTS `#@__groups`;
DROP TABLE IF EXISTS `#@__group_guestbook`;
DROP TABLE IF EXISTS `#@__group_notice`;
DROP TABLE IF EXISTS `#@__group_posts`;
DROP TABLE IF EXISTS `#@__group_threads`;
DROP TABLE IF EXISTS `#@__group_user`;
DROP TABLE IF EXISTS `#@__store_groups`;
DROP TABLE IF EXISTS `#@__group_smalltypes`;

delete from `#@__sysconfig` where varname='cfg_group_creators' limit 1;
delete from `#@__sysconfig` where varname='cfg_group_max' limit 1;
delete from `#@__sysconfig` where varname='cfg_group_click' limit 1;
delete from `#@__sysconfig` where varname='cfg_group_maxuser' limit 1;
delete from `#@__sysconfig` where varname='cfg_group_words' limit 1;

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

$menunew = preg_replace("/#group_menu_start#(.*)#group_menu_end#/is",'',$menuold);

$fp = fopen($menufile,"w");
fwrite($fp,$menunew);
fclose($fp);

//个人会员菜单
$membermenuold = '';
$membermenufile = DEDEADMIN.'/../member/templets/menu.php';
$fp = fopen($membermenufile,'r') Or die("File： {$membermenufile} not found!");
while(!feof($fp)) $membermenuold .= fread($fp, 8192);
fclose($fp);

$membermenunew = preg_replace("/<!--#group_menu_start#-->(.*)<!--#group_menu_end#-->/is",'',$membermenuold);

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