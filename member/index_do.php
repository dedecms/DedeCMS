<?php
require_once(dirname(__FILE__)."/config.php");
if(empty($dopost))
{
	$dopost = '';
}
if(empty($fmdo))
{
	$fmdo = '';
}

/*********************
function Case_user()
*******************/
if($fmdo=='user')
{

	//检查用户名是否存在
	if($dopost=="checkuser")
	{
		AjaxHead();
		$msg = '';
		$uid = trim($uid);
		if($cktype==0)
		{
			$msgtitle='用户昵称';
		}
		else
		{
			$msgtitle='用户名';
		}
		$msg = CheckUserID($uid,$msgtitle);
		if($msg=='ok')
		{
			$msg = "<font color='#4E7504'><b>√{$msgtitle}可以使用</b></font>";
		}
		else
		{
			$msg = "<font color='red'><b>×{$msg}</b></font>";
		}
		echo $msg;
		exit();
	}

	//检查email是否存在
	else  if($dopost=="checkmail")
	{
		AjaxHead();
		if($cfg_md_mailtest=='N')
		{
			$msg = "<font color='#4E7504'><b>√可以使用</b></font>";
		}
		else
		{
			if(!eregi("^[0-9a-z][a-z0-9\.-]{1,}@[a-z0-9-]{1,}[a-z0-9]\.[a-z\.]{1,}[a-z]$",$email))
			{
				$msg = "<font color='#4E7504'><b>×Email格式有误</b></font>";
			}
			else
			{
				 $row = $dsql->GetOne("Select mid From `#@__member` where email like '$email' limit 1");
				 if(!is_array($row)) {
					 $msg = "<font color='#4E7504'><b>√可以使用</b></font>";
				 }
				 else {
					 $msg = "<font color='red'><b>×Email已经被另一个帐号占用！</b></font>";
				 }
			}
		}
		echo $msg;
		exit();
	}

	//引入注册页面
	else if($dopost=="regnew")
	{

		require_once(dirname(__FILE__)."/reg_new.php");
		exit();
	}

	//申请升级
	else if($dopost=="uprank")
	{
		CheckRank(0,0);
		if(empty($uptype))
		{
			ShowMsg("数据无效！","-1");
			exit();
		}
		$uptype = GetAlabNum($uptype);
		if($uptype < $cfg_ml->M_Rank)
		{
			ShowMsg("类型不对，你的级别比你目前申请的级别还要高！","-1");
			exit();
		}
		$dsql->SetQuery("update `#@__member` set `uprank`='$uptype' where mid='".$cfg_ml->M_ID."' ");
		$dsql->Execute();
		ShowMsg("成功申请升级，请等待管理员开通！","index.php?".time());
		exit();
	}

	//升级金币
	else if($dopost=="addmoney")
	{
		CheckRank(0,0);
		$svali = GetCkVdValue();
		if(strtolower($vdcode)!=$svali || $svali=="")
		{
			ResetVdValue();
			ShowMsg("验证码错误！","-1");
			exit();
		}
		if(empty($money))
		{
			ShowMsg("你没指定要申请多少金币！","-1");
			exit();
		}
		$dsql->SetQuery("update #@__member set upmoney='$money' where mid='".$cfg_ml->M_ID."'");
		$dsql->Execute();
		ShowMsg("成功提交你的申请！","index.php?".time());
		exit();
	}


}

/*********************
function login()
*******************/
else if($fmdo=='login')
{

	//用户登录
	if($dopost=="login")
	{
		if(!isset($vdcode))
		{
			$vdcode = '';
		}
		$svali = GetCkVdValue();
		if(strtolower($vdcode)!=$svali || $svali=='')
		{
			ResetVdValue();
			ShowMsg("验证码错误！","-1");
			exit();
		}
		if(CheckUserID($userid,'',false)!='ok')
		{
			ShowMsg("你输入的用户名 {$userid} 不合法！","-1");
			exit();
		}
		if($pwd=='')
		{
			ShowMsg("密码不能为空！","-1",0,2000);
			exit();
		}

		//检查帐号
		$rs = $cfg_ml->CheckUser($userid,$pwd);
		if($rs==0)
		{
			ShowMsg("用户名不存在！","-1",0,2000);
			exit();
		}
		else if($rs==-1) {
			ShowMsg("密码错误！","-1",0,2000);
			exit();
		}
		else if($rs==-2) {
			ShowMsg("管理员帐号不允许从前台登录！","-1",0,2000);
			exit();
		}
		else
		{
			if(empty($gourl) || eregi("action|_do",$gourl))
			{
				ShowMsg("成功登录，5秒钟后转向系统主页...","index.php",0,2000);
			}
			else
			{
				ShowMsg("成功登录，现在转向指定页面...",$gourl,0,2000);
			}
			exit();
		}
	}

	//退出登录
	else if($dopost=="exit")
	{
		$cfg_ml->ExitCookie();
		ShowMsg("成功退出登录！","index.php",0,2000);
		exit();
	}
}
else
{
	ShowMsg("本页面禁止返回!","index.php");
}

?>