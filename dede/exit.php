<?php
require_once(dirname(__FILE__)."/../include/common.inc.php");
require_once(DEDEINC."/userlogin.class.php");
$cuserLogin = new userLogin();
$cuserLogin->exitUser();
header("location:index.php");
?>