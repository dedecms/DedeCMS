<?php
require_once(dirname(__FILE__).'/../include/common.inc.php');
require_once(DEDEINC.'/userlogin.class.php');
$cuserLogin = new userLogin();
$cuserLogin->exitUser();
if(empty($needclose))
{
	header('location:index.php');
}
else
{
	$msg = "<script language='javascript'>
	if(document.all) window.opener=true;
	window.close();
	</script>";
	echo $msg;
}
?>