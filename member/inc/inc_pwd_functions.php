<?php
if(!defined('DEDEMEMBER'))
{
	exit("dedecms");
}

//验证码生成函数
function random($length, $numeric = 0)
{
	PHP_VERSION < '4.2.0' && mt_srand((double)microtime() * 1000000);
	if($numeric)
	{
		$hash = sprintf('%0'.$length.'d', mt_rand(0, pow(10, $length) - 1));
	}
	else
	{
		$hash = '';
		$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz';
		$max = strlen($chars) - 1;
		for($i = 0; $i < $length; $i++)
		{
			$hash .= $chars[mt_rand(0, $max)];
		}
	}
	return $hash;
}

//邮件发送函数
function sendmail($email, $mailtitle, $mailbody, $headers)
{
	global $cfg_sendmail_bysmtp, $cfg_smtp_server, $cfg_smtp_port, $cfg_smtp_usermail, $cfg_smtp_user, $cfg_smtp_password, $cfg_adminemail;
	if($cfg_sendmail_bysmtp == 'Y')
	{
		$mailtype = 'TXT';
		require_once(DEDEINC.'/mail.class.php');
		$smtp = new smtp($cfg_smtp_server,$cfg_smtp_port,true,$cfg_smtp_usermail,$cfg_smtp_password);
		$smtp->debug = false;
		$smtp->sendmail($email,$cfg_webname,$cfg_smtp_usermail, $mailtitle, $mailbody, $mailtype);
	}
	else
	{
		@mail($email, $mailtitle, $mailbody, $headers);
	}
}

//发送邮件；type为INSERT新建验证码，UPDATE修改验证码；
function newmail($mid,$userid,$mailto,$type,$send)
{
	global $db,$cfg_adminemail,$cfg_webname,$cfg_basehost,$cfg_memberurl;
	$mailtime = time();
	$randval = random(8);
	$mailtitle = $cfg_webname.":密码修改";
	$mailto = $mailto;
	$headers = "From: ".$cfg_adminemail."\r\nReply-To: $cfg_adminemail";
	$mailbody = "亲爱的".$userid."：\r\n您好！感谢您使用".$cfg_webname."网。\r\n".$cfg_webname."应您的要求，重新设置密码：（注：如果您没有提出申请，请检查您的信息是否泄漏。）\r\n本次临时登陆密码为：".$randval." 请于三天内登陆下面网址确认修改。\r\n".$cfg_basehost.$cfg_memberurl."/resetpassword.php?dopost=getpasswd&id=".$mid;
	if($type == 'INSERT')
	{
		$key = md5($randval);
		$sql = "INSERT INTO `#@__pwd_tmp` (`mid` ,`membername` ,`pwd` ,`mailtime`)VALUES ('$mid', '$userid',  '$key', '$mailtime');";
		if($db->ExecuteNoneQuery($sql))
		{
			if($send == 'Y')
			{
				sendmail($mailto,$mailtitle,$mailbody,$headers);
				return showmsg('EMAIL修改验证码已经发送到原来的邮箱请查收', 'login.php','','5000');
			}
			elseif($send == 'N')
			{
				return showmsg('稍后跳转到修改页', $cfg_basehost.$cfg_memberurl."/resetpassword.php?dopost=getpasswd&amp;id=".$mid."&amp;key=".$randval);
			}
		}
		else
		{
			return showmsg('对不起修改失败，请联系管理员', 'login.php');
		}
	}
	elseif($type == 'UPDATE')
	{
		$key = md5($randval);
		$sql = "UPDATE `#@__pwd_tmp` SET `pwd` = '$key',mailtime = '$mailtime'  WHERE `mid` ='$mid';";
		if($db->ExecuteNoneQuery($sql))
		{
			if($send == 'Y')
			{
				sendmail($mailto,$mailtitle,$mailbody,$headers);
				showmsg('EMAIL修改验证码已经发送到原来的邮箱请查收', 'login.php');
			}
			elseif($send == 'N')
			{
				return showmsg('稍后跳转到修改页', $cfg_basehost.$cfg_memberurl."/resetpassword.php?dopost=getpasswd&amp;id=".$mid."&amp;key=".$randval);
			}
		}
		else
		{
			showmsg('对不起修改失败，请与管理员联系', 'login.php');
		}
	}
}

//查询会员信息mail用户输入邮箱地址；userid用户名
function member($mail,$userid)
{
	global $db;
	$sql = "Select mid,email,safequestion From #@__member where email='$mail' AND userid = '$userid'";
	$row = $db->GetOne($sql);
	if(!is_array($row))
	{
		return ShowMsg("对不起，用户ID输入错误！","-1");
	}
	else
	{
		return $row;
	}
}

//查询是否发送过验证码,mid为会员ID；userid为会员名称；mailto发送邮件地址；send为Y发送邮件，为N不发送邮件默认为Y
function sn($mid,$userid,$mailto, $send = 'Y')
{
	global $db;
	$tptim= (60*10);
	$dtime = time();
	$sql = "Select * From #@__pwd_tmp where mid = '$mid'";
	$row = $db->GetOne($sql);
	if(!is_array($row))
	{

		//发送新邮件；
		newmail($mid,$userid,$mailto,'INSERT',$send);
	}

	//10分钟后可以再次发送新验证码；
	elseif($dtime - $tptim > $row['mailtime'])
	{
		newmail($mid,$userid,$mailto,'UPDATE',$send);
	}

	//重新发送新的验证码确认邮件；
	else
	{
		return showmsg('对不起，请10分钟后再重新申请', 'login.php');
	}
}
?>