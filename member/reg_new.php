<?php
require_once(dirname(__FILE__)."/config.php");
if($cfg_mb_allowreg=='N')
{
	ShowMsg('系统关闭了新用户注册！', 'index.php');
	exit();
}
if($cfg_ml->IsLogin())
{
	ShowMsg('你已经登陆系统，无需重新注册！', 'index.php');
	exit();
}
if(!isset($dopost))
{
	$dopost = '';
}
if($dopost=='regok')
{
	$svali = GetCkVdValue();
	if(strtolower($vdcode)!=$svali || $svali=='')
	{
		ResetVdValue();
		ShowMsg('验证码错误！', '-1');
		exit();
	}
	$userid = trim($userid);
	$pwd = trim($userpwd);
	$pwdc = trim($userpwdok);
	$rs = CheckUserID($userid, '用户名');
	if($rs != 'ok')
	{
		ShowMsg($rs, '-1');
		exit();
	}
	if(strlen($userid) > 20 || strlen($uname) > 36)
	{
		ShowMsg('你的用户名或用户笔名过长，不允许注册！', '-1');
		exit();
	}
	if(strlen($userid) < $cfg_mb_idmin || strlen($pwd) < $cfg_mb_pwdmin)
	{
		ShowMsg("你的用户名或密码过短，不允许注册！","-1");
		exit();
	}
	if($pwdc != $pwd)
	{
		ShowMsg('你两次输入的密码不一致！', '-1');
		exit();
	}
	
	$uname = HtmlReplace($uname, 1);
	//用户笔名重复检测
	if($cfg_mb_wnameone=='N')
	{
		$row = $dsql->GetOne("Select * From `#@__member` where uname like '$uname' ");
		if(is_array($row))
		{
			ShowMsg('用户笔名或公司名称不能重复！', '-1');
			exit();
		}
	}
	if(!CheckEmail($email))
	{
		ShowMsg('Email格式不正确！', '-1');
		exit();
	}
	
	#api{{
	if(defined('UC_API') && @include_once DEDEROOT.'/uc_client/client.php')
	{
		$uid = uc_user_register($userid, $pwd, $email);
		if($uid <= 0)
		{
			if($uid == -1)
			{
				ShowMsg("用户名不合法！","-1");
				exit();
			}
			elseif($uid == -2)
			{
				ShowMsg("包含要允许注册的词语！","-1");
				exit();
			}
			elseif($uid == -3)
			{
				ShowMsg("你指定的用户名 {$userid} 已存在，请使用别的用户名！","-1");
				exit();
			}
			elseif($uid == -5)
			{
				ShowMsg("你使用的Email 不允许注册！","-1");
				exit();
			}
			elseif($uid == -6)
			{
				ShowMsg("你使用的Email已经被另一帐号注册，请使其它帐号","-1");
				exit();
			}
			else
			{
				ShowMsg("注删失改！","-1");
				exit();
			}
		}
		else
		{
			$ucsynlogin = uc_user_synlogin($uid);
		}
	}
	#/aip}}
	
	if($cfg_md_mailtest=='Y')
	{
		$row = $dsql->GetOne("Select mid From `#@__member` where email like '$email' ");
		if(is_array($row))
		{
			ShowMsg('你使用的Email已经被另一帐号注册，请使其它帐号！', '-1');
			exit();
		}
	}

	//检测用户名是否存在
	$row = $dsql->GetOne("Select mid From `#@__member` where userid like '$userid' ");
	if(is_array($row))
	{
		ShowMsg("你指定的用户名 {$userid} 已存在，请使用别的用户名！", "-1");
		exit();
	}
	if($safequestion==0)
	{
		$safeanswer = '';
	}
	else
	{
		if(strlen($safeanswer)>30)
		{
			ShowMsg('你的新安全问题的答案太长了，请控制在30字节以内！', '-1');
			exit();
		}
	}

	//会员的默认金币
	$dfscores = 0;
	$dfmoney = 0;
	$dfrank = $dsql->GetOne("Select money,scores From `#@__arcrank` where rank='10' ");
	if(is_array($dfrank))
	{
		$dfmoney = $dfrank['money'];
		$dfscores = $dfrank['scores'];
	}
	$jointime = time();
	$logintime = time();
	$joinip = GetIP();
	$loginip = GetIP();
	$pwd = md5($userpwd);
	
	$spaceSta = ($cfg_mb_spacesta < 0 ? $cfg_mb_spacesta : 0);
	
	$inQuery = "INSERT INTO `#@__member` (`mtype` ,`userid` ,`pwd` ,`uname` ,`sex` ,`rank` ,`money` ,`email` ,`scores` ,
	`matt`, `spacesta` ,`face`,`safequestion`,`safeanswer` ,`jointime` ,`joinip` ,`logintime` ,`loginip` )
   VALUES ('$mtype','$userid','$pwd','$uname','$sex','10','$dfmoney','$email','$dfscores',
   '0','$spaceSta','','$safequestion','$safeanswer','$jointime','$joinip','$logintime','$loginip'); ";
	if($dsql->ExecuteNoneQuery($inQuery))
	{
		$mid = $dsql->GetLastID();

		//写入默认会员详细资料
		if($mtype=='个人')
		{
			$infosquery = "INSERT INTO `#@__member_person` (`mid` , `onlynet` , `sex` , `uname` , `qq` , `msn` , `tel` , `mobile` , `place` , `oldplace` ,
	           `birthday` , `star` , `income` , `education` , `height` , `bodytype` , `blood` , `vocation` , `smoke` , `marital` , `house` ,
	            `drink` , `datingtype` , `language` , `nature` , `lovemsg` , `address`,`uptime`)
             VALUES ('$mid', '1', '{$sex}', '{$uname}', '', '', '', '', '0', '0',
              '1980-01-01', '1', '0', '0', '160', '0', '0', '0', '0', '0', '0','0', '0', '', '', '', '','0'); ";
			$space='person';
		}
		else if($mtype=='企业')
		{
			$infosquery = "INSERT INTO `#@__member_company`(`mid`,`company`,`product`,`place`,`vocation`,`cosize`,`tel`,`fax`,`linkman`,`address`,`mobile`,`email`,`url`,`uptime`,`checked`,`introduce`)
                VALUES ('{$mid}','{$uname}','product','0','0','0','','','','','','{$email}','','0','0',''); ";
			$space='company';
		}
		else
		{
			$infosquery = '';
			$space='person';
		}
		/** 此处增加不同类别会员的特殊数据处理sql语句 **/

		$dsql->ExecuteNoneQuery($infosquery);

		//写入默认统计数据
		$membertjquery = "INSERT INTO `#@__member_tj` (`mid`,`article`,`album`,`archives`,`homecount`,`pagecount`,`feedback`,`friend`,`stow`)
               VALUES ('$mid','0','0','0','0','0','0','0','0'); ";
		$dsql->ExecuteNoneQuery($membertjquery);

		//写入默认空间配置数据
		$spacequery = "Insert Into `#@__member_space`(`mid` ,`pagesize` ,`matt` ,`spacename` ,`spacelogo` ,`spacestyle`, `sign` ,`spacenews`)
	            Values('{$mid}','10','0','{$uname}的空间','','$space','',''); ";
		$dsql->ExecuteNoneQuery($spacequery);

		//写入其它默认数据
		$dsql->ExecuteNoneQuery("INSERT INTO `#@__member_flink`(mid,title,url) VALUES('$mid','织梦内容管理系统','http://www.dedecms.com'); ");

		//----------------------------------------------
		//模拟登录
		//---------------------------
		$cfg_ml = new MemberLogin(7*3600);
		$rs = $cfg_ml->CheckUser($userid, $userpwd);
		
	//邮件验证
	if($cfg_mb_spacesta==-10)
	{
		$userhash = md5($cfg_cookie_encode.'--'.$mid.'--'.$email);
  	$url = $cfg_basehost.(empty($cfg_cmspath) ? '/' : $cfg_cmspath)."/member/index_do.php?fmdo=checkMail&mid={$mid}&userhash={$userhash}&do=1";
  	$url = eregi_replace('http://', '', $url);
  	$url = 'http://'.eregi_replace('//', '/', $url);
  	$mailtitle = "{$cfg_webname}--会员邮件验证通知";
  	$mailbody = '';
  	$mailbody .= "尊敬的用户，您好：\r\n";
  	$mailbody .= "欢迎注册成为[{$cfg_webname}]的会员。\r\n";
  	$mailbody .= "要通过注册，还必须进行最后一步操作，请点击或复制下面链接到地址栏访问这地址：\r\n\r\n";
  	$mailbody .= "{$url}\r\n\r\n";
  	$mailbody .= "Power by http://www.dedecms.com 织梦内容管理系统！\r\n";
  
		$headers = "From: ".$cfg_adminemail."\r\nReply-To: ".$cfg_adminemail;
		if($cfg_sendmail_bysmtp == 'Y' && !empty($cfg_smtp_server))
		{		
			$mailtype = 'TXT';
			require_once(DEDEINC.'/mail.class.php');
			$smtp = new smtp($cfg_smtp_server,$cfg_smtp_port,true,$cfg_smtp_usermail,$cfg_smtp_password);
			$smtp->debug = false;
			$smtp->sendmail($email, $cfg_smtp_usermail, $mailtitle, $mailbody, $mailtype);
		}
		else
		{
			@mail($email, $mailtitle, $mailbody, $headers);
		}
	}//End 邮件验证
		
		
		ShowMsg("注册成功，3秒钟后转向系统主页...","index.php",0,2000);
		exit();
	}
	else
	{
		ShowMsg("注册失败，请检查资料是否有误或与管理员联系！", "-1");
		exit();
	}
}

	$sql = "desc #@__member";
	$dsql->SetQuery($sql);
	$dsql->Execute();
	while ($row = $dsql->GetArray()) {
		if($row['Field'] == 'mtype')
		{
			$types = $row['Type'];
			break;
		}
	}
	$types = str_replace(array('enum', '(', ')', '\''), '', $types);
	$types = explode(',', $types);


require_once(DEDEMEMBER."/templets/reg-new.htm");
?>