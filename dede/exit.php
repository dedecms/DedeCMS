<?
require_once("inc_userLogin.php");
require_once("config_base.php");
$cuserLogin = new userLogin();
$cuserLogin->exitUser();
header("location:index.php");
?>