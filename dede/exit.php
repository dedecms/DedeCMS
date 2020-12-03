<?php 
require_once(dirname(__FILE__)."/../include/inc_userlogin.php");
require_once(dirname(__FILE__)."/../include/config_base.php");
$cuserLogin = new userLogin();
$cuserLogin->exitUser();
header("location:index.php");
?>