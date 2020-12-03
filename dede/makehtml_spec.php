<?php
require_once(dirname(__FILE__)."/config.php");
CheckPurview('sys_MakeHtml');
if(empty($dopost))
{
	$dopost = "";
}

if($dopost=="ok")
{
	require_once(DEDEINC."/arc.specview.class.php");
	$sp = new SpecView();
	$rurl = $sp->MakeHtml();
	echo "成功生成所有专题HTML列表！<a href='$rurl' target='_blank'>预览</a>";
	exit();
}
include DedeInclude('templets/makehtml_spec.htm');

?>