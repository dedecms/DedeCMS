<?php
require_once(dirname(__FILE__)."/config.php");
AjaxHead();
if($myurl == '')
{
	exit('');
}
$uid  = $cfg_ml->M_LoginID;
$face = $cfg_ml->fields['face'] == '' ? $GLOBALS['cfg_memberurl'].'/images/nopic.gif' : $cfg_ml->fields['face'];
?>
用户名：<?php echo $cfg_ml->M_UserName; ?> <input name="notuser" type="checkbox" id="notuser" value="1" /> 匿名评论
<?php if($cfg_feedback_ck=='Y') { ?>
验证码：<input name="validate" type="text" id="validate" size="10" style="height:18px;width:60px;margin-right:6px;" class="nb" />
<img src='<?php echo $cfg_cmsurl;?>/include/vdimgck.php' width='60' height='24' />
<?php } ?>
