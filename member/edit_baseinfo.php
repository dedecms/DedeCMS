<?php
require_once(dirname(__FILE__)."/config.php");
CheckRank(0,0);
if(!isset($dopost))
{
	$dopost = '';
}
$row=$dsql->GetOne("select  * from `#@__member` where mid='".$cfg_ml->M_ID."'");
$face = $row['face'];
if($dopost=='save')
{
	$svali = GetCkVdValue();

	if(strtolower($vdcode) != $svali || $svali=='')
	{
		ResetVdValue();
		ShowMsg('验证码错误！','-1');
		exit();
	}
	if(!is_array($row) || $row['pwd']!=md5($oldpwd))
	{
		ShowMsg('你输入的旧密码错误或没填写，不允许修改资料！','-1');
		exit();
	}
	if($userpwd!=$userpwdok)
	{
		ShowMsg('你两次输入的新密码不一致！','-1');
		exit();
	}
	if($userpwd=='')
	{
		$pwd = $row['pwd'];
	}
	else
	{
		$pwd = md5($userpwd);
		$pwd2 = substr(md5($userpwd),5,20);
	}
	$addupquery = '';

	//修改安全问题或Email
	if($email != $row['email'] || ($newsafequestion != 0 && $newsafeanswer != ''))
	{
		if($row['safequestion']!=0 && ($row['safequestion'] != $safequestion || $row['safeanswer'] != $safeanswer))
		{
			ShowMsg('你的旧安全问题及答案不正确，不能修改Email或安全问题！','-1');
			exit();
		}

		//修改Email
		if($email != $row['email'])
		{
			if(!eregi("^[0-9a-z][a-z0-9\.-]{1,}@[a-z0-9-]{1,}[a-z]\.[a-z\.]{1,}[a-z]$",$email))
			{
				ShowMsg('Email格式不正确！','-1');
				exit();
			}
			else
			{
				$addupquery .= ",email='$email'";
			}
		}

		//修改安全问题
		if($newsafequestion != 0 && $newsafeanswer != '')
		{
			if(strlen($newsafeanswer) > 30)
			{
				ShowMsg('你的新安全问题的答案太长了，请保持在30字节以内！','-1');
				exit();
			}
			else
			{
				$addupquery .= ",safequestion='$newsafequestion',safeanswer='$newsafeanswer'";
			}
		}
	}

	//修改uname
	if($uname != $row['uname'])
	{
		$rs = CheckUserID($uname,'昵称或公司名称');
		if($rs!='ok')
		{
			ShowMsg($rs,'-1');
			exit();
		}
		$addupquery .= ",uname='$uname'";
	}
	
	//性别
	if( !in_array($sex,array('男','女','保密')) )
	{
		ShowMsg('请选择正常的性别！','-1');
		exit();	
	}
	
	$query1 = "Update `#@__member` set pwd='$pwd',sex='$sex'{$addupquery} where mid='".$cfg_ml->M_ID."' ";
	$dsql->ExecuteNoneQuery($query1);

	//如果是管理员，修改其后台密码
	if($cfg_ml->fields['matt']==10 && $pwd2!="")
	{
		$query2 = "Update `#@__admin` set pwd='$pwd2' where id='".$cfg_ml->M_ID."' ";
		$dsql->ExecuteNoneQuery($query2);
	}
	ShowMsg('成功更新你的基本资料！','edit_baseinfo.php',0,5000);
	exit();
}
include(DEDEMEMBER."/templets/edit_baseinfo.htm");
?>