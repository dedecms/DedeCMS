<?
$tt = time();
setcookie("cookie_user","",$tt-36000,"/");
setcookie("cookie_username","",$tt-36000,"/");
setcookie("cookie_rank","",$tt-36000,"/");
setcookie("cookie_isup","",$tt-36000,"/");
echo "<script>\n";
echo "alert('成功退出会员中心！');\n";
echo "location.href='/';\n";
echo "</script>\n";
?>