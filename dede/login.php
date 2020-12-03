<?php
require_once(dirname(__FILE__).'/../include/common.inc.php');
require_once(DEDEINC.'/userlogin.class.php');
if(empty($dopost))
{
	$dopost = '';
}

//检测安装目录安全性
if( is_dir(dirname(__FILE__).'/../install') )
{
	if(!file_exists(dirname(__FILE__).'/../install/install_lock.txt') )
	{
  	$fp = fopen(dirname(__FILE__).'/../install/install_lock.txt', 'w') or die('安装目录无写入权限，无法进行写入锁定文件，请安装完毕删除安装目录！');
  	fwrite($fp,'ok');
  	fclose($fp);
	}
	//为了防止未知安全性问题，强制禁用安装程序的文件
	if( file_exists("../install/index.php") ) {
		@rename("../install/index.php", "../install/index.php.bak");
	}
	if( file_exists("../install/module-install.php") ) {
		@rename("../install/module-install.php", "../install/module-install.php.bak");
	}
}
//检测后台目录是否更名
$cururl = GetCurUrl();
if(preg_match('/dede\/login/i',$cururl))
{
	$redmsg = '<br />&nbsp;&nbsp;&nbsp;&nbsp;<font color=\'red\'><b>您的管理目录使用默认名称dede，建议在FTP里把它修改为其它名称，那样会更安全！</b></font>';
}
else
{
	$redmsg = '';
}

//登录检测
$admindirs = explode('/',str_replace("\\",'/',dirname(__FILE__)));
$admindir = $admindirs[count($admindirs)-1];
if($dopost=='login')
{
	$validate = empty($validate) ? '' : strtolower(trim($validate));
	$svali = strtolower(GetCkVdValue());
	if(($validate=='' || $validate != $svali) && preg_match("/6/",$safe_gdopen)){
		ResetVdValue();
		ShowMsg('验证码不正确!','');
	} else {
		$cuserLogin = new userLogin($admindir);
		if(!empty($userid) && !empty($pwd))
		{
			$res = $cuserLogin->checkUser($userid,$pwd);

			//success
			if($res==1)
			{
				$cuserLogin->keepUser();
				if(!empty($gotopage))
				{
					ShowMsg('成功登录，正在转向管理管理主页！',$gotopage);
					exit();
				}
				else
				{
					ShowMsg('成功登录，正在转向管理管理主页！',"index.php");
					exit();
				}
			}

			//error
			else if($res==-1)
			{
				ShowMsg('你的用户名不存在!','');
			}
			else
			{
				ShowMsg('你的密码错误!','');
			}
		}

		//password empty
		else
		{
			ShowMsg('用户和密码没填写完整!','');
		}
	}
}
include('templets/login.htm');

?>