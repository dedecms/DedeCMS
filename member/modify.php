<?php
//print_r($_POST);exit;
if(empty($_POST) && empty($_GET))
{
	ShowMsg("本页面禁止返回!","control.php");
	exit();
}
require_once(dirname(__FILE__)."/config.php");

if(empty($action))
{
	$action = '';
}
if($action == 'editcominfo')
{
	CheckRank(0,0);
	$svali = GetCkVdValue();
	/*
	if(strtolower($vdcode) != $svali || $svali == '')
	{
		ShowMsg("验证码错误！","-1");
		exit();
	}
	*/
	if($email != '' && $email != $oldemail)
	{
		echo "dd";//此处执行email的更新
	}
$typeid1=$typeid2=0;
/*
	$sql = "update #@__member_cominfo set truename='$truename', business='$business',
	phone='$phone', fax='$fax', mobi='$mobi', comname='$comname',
	regyear='$regyear', regaddr='$regaddr', service='$service', typeid1='$typeid1',
	typeid2='$typeid2', comaddr='$comaddr', cominfo='$cominfo',
	postid='$postid', website='$website' where id='{$cfg_ml->M_ID}'";
*/
	$sql = "REPLACE INTO #@__member_cominfo (id, truename, business, phone, fax, mobi,
		comname, regyear, regaddr, service, typeid1, typeid2, comaddr,
		cominfo, postid, website)
	VALUES('$cfg_ml->M_ID', '$truename',
		'$business', '$phone', '$fax', '$mobi', '$comname', '$regyear',
		'$regaddr', '$service', '$typeid1', '$typeid2', '$comaddr',
		'$cominfo', '$postid', '$website')";
	$db = new DedeSql(false);
	$db->SetQuery($sql);//exit;
	if(!$db->ExecuteNoneQuery())
	{
		$db->Close();
		ShowMsg("更改资料出错，请检查输入是否合法！","-1");
		exit();
	}else{
		$db->Close();
		ShowMsg("成功更新你的个人资料！","mycominfo.php");
		exit();
	}

}


?>