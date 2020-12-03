<?php
require_once(dirname(__FILE__)."/../config.php");

$sql = "
DROP TABLE IF EXISTS `#@__ask`;
DROP TABLE IF EXISTS `#@__askanswer`;
DROP TABLE IF EXISTS `#@__asktype`;

delete from `#@__sysconfig` where 
varname='cfg_ask', 
Or varname='cfg_ask_ifcheck', 
Or varname='cfg_ask_dateformat',
Or varname='cfg_ask_timeformat',
Or varname='cfg_ask_timeoffset',
Or varname='cfg_ask_gzipcompress',
Or varname='cfg_ask_authkey',
Or varname='cfg_ask_cookiepre',
Or varname='cfg_answer_ifcheck',
Or varname='cfg_ask_expiredtime',
Or varname='cfg_ask_tpp',
Or varname='cfg_ask_sitename',,
Or varname='cfg_ask_symbols',
Or varname='cfg_ask_answerscore',
Or varname='cfg_ask_bestanswer',
Or varname='cfg_ask_subtypenum'
";

$db = new DedeSql(false);
$sqls = explode(';', $sql);
foreach($sqls as $sql){
	if(trim($sql)!='') $db->executenonequery($sql);
}
$db->Close();

//删除管理菜单
$menuold = '';
$menufile = DEDEADMIN.'/inc/inc_menu.php';
$fp = fopen($menufile,'r') Or die("File： {$menufile} not found!");
while(!feof($fp)){ $menuold .= fread($fp, 8192); }
fclose($fp);

$menunew = preg_replace("/#ask_menu_start#(.*)#ask_menu_end#/is",'',$menuold);

$fp = fopen($menufile,"w");
fwrite($fp,$menunew);
fclose($fp);

//个人会员菜单
$membermenuold = '';
$membermenufile = DEDEADMIN.'/../member/templets/menu.php';
$fp = fopen($membermenufile,'r') Or die("File： {$membermenufile} not found!");
while(!feof($fp)) $membermenuold .= fread($fp, 8192);
fclose($fp);

$membermenunew = preg_replace("/<!--#ask_menu_start#-->(.*)<!--#ask_menu_end#-->/is",'',$membermenuold);

$fp = fopen($membermenufile,'w');
fwrite($fp,$membermenunew);
fclose($fp);

//企业会员菜单
$membermenuold = '';
$membermenufile = DEDEADMIN.'/../member/templets/commenu.php';
$fp = fopen($membermenufile,'r') Or die("File： {$membermenufile} not found!");
while(!feof($fp)) $membermenuold .= fread($fp, 8192);
fclose($fp);

$membermenunew = preg_replace("/<!--#ask_menu_start#-->(.*)<!--#ask_menu_end#-->/is",'',$membermenuold);

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