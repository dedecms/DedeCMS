<?php

/**
 * Enter description here...
 *
 * @author Administrator
 * @package defaultPackage
 * @rcsfile 	$RCSfile: loginsta.php,v $
 * @revision 	$Revision: 1.1 $
 * @date 	$Date: 2009/08/04 04:06:24 $
 */

header("Pragma:no-cache\r\n");
header("Cache-Control:no-cache\r\n");
header("Expires:0\r\n");
header("Content-Type: text/html; charset=UTF-8");
require_once(dirname(__FILE__)."/../include/common.inc.php");
require_once(DEDEINC."/memberlogin.class.php");
$cfg_ml = new MemberLogin();
if(empty($cfg_ml->M_ID))
{
	echo "您好，请 <span><a href='$cfg_memberurl/login.php?gourl=$cfg_cmsurl/book'>登录</a></span> 或 <span><a href='{$cfg_memberurl}/index_do.php?fmdo=user&dopost=regnew'>注册</a>\r\n";
	exit();
}
?>
你好：<font color='#2D78EA'> <?php echo $cfg_ml->M_UserName; ?> </font>，欢迎登录
<a href="<?php echo $cfg_memberurl; ?>/mystow.php">我的收藏</a> |
<a href="<?php echo $cfg_memberurl; ?>/index.php">控制面板</a> |
<a href="<?php echo $cfg_memberurl; ?>/index_do.php?fmdo=login&dopost=exit">退出系统</a>