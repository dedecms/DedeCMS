<?php
require(dirname(__FILE__)."/config.php");
CheckPurview('member_Edit');
$ENV_GOBACK_URL = isset($_COOKIE['ENV_GOBACK_URL']) ? "member_main.php" : '';
$id = ereg_replace("[^0-9]","",$id);
$row = $dsql->GetOne("select  * from #@__member where mid='$id'");

//如果这个用户是管理员帐号，必须有足够权限的用户才能操作
if($row['matt']==10)
{
	CheckPurview('sys_User');
}
include DedeInclude('templets/member_view.htm');

?>