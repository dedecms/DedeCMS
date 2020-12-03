<?php
require_once(dirname(__FILE__)."/config.php");
CheckPurview('sys_MakeHtml');
$isremote = empty($isremote)? 0 : $isremote;
$serviterm=empty($serviterm)? "" : $serviterm;
if(empty($dopost))
{
	$dopost = "";
}

if($dopost=="ok")
{
	require_once(DEDEINC."/arc.specview.class.php");
	if($cfg_remote_site=='Y' && $isremote=="1")
	{	
		if($serviterm!=""){
			list($servurl,$servuser,$servpwd) = explode(',',$serviterm);
			$config=array( 'hostname' => $servurl, 'username' => $servuser, 'password' => $servpwd,'debug' => 'TRUE');
		}else{
			$config=array();
		}
		if(!$ftp->connect($config)) exit('Error:None FTP Connection!');
	}
	$sp = new SpecView();
	$rurl = $sp->MakeHtml($isremote);
	echo "成功生成所有专题HTML列表！<a href='$rurl' target='_blank'>预览</a>";
	exit();
}
include DedeInclude('templets/makehtml_spec.htm');

?>