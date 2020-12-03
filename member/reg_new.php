<?php
require_once(dirname(__FILE__)."/config.php");
if($cfg_ml->IsLogin())
{
	ShowMsg("你已经登陆系统，无需重新注册！","index.php");
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
		ShowMsg("验证码错误！","-1");
		exit();
	}
	$userid = trim($userid);
	$pwd = trim($userpwd);
	$pwdc = trim($userpwdok);
	$rs = CheckUserID($userid,'用户名');
	if($rs != 'ok')
	{
		ShowMsg($rs,"-1");
		exit();
	}
	if(strlen($userid) > 20 || strlen($uname) > 36)
	{
		ShowMsg("你的用户名或昵称/公司名称过长，不允许注册！","-1");
		exit();
	}
	if(strlen($userid) < $cfg_mb_idmin || strlen($pwd) < $cfg_mb_pwdmin)
	{
		ShowMsg("你的用户名或密码过短，不允许注册！","-1");
		exit();
	}
	if($pwdc!=$pwd)
	{
		ShowMsg("你两次输入的密码不一致！","-1");
		exit();
	}
	if(CheckUserID($uname,'')!='ok')
	{
		ShowMsg("用户昵称或公司名称含有非法字符！","-1");
		exit();
	}
	if(!eregi("^[0-9a-z][a-z0-9\.-]{1,}@[a-z0-9-]{1,}[a-z0-9]\.[a-z\.]{1,}[a-z]$",$email))
	{
		ShowMsg("Email格式不正确！","-1");
		exit();
	}
	if($cfg_md_mailtest=='Y')
	{
		$row = $dsql->GetOne("Select mid From `#@__member` where email like '$email' ");
		if(is_array($row))
		{
			ShowMsg("你使用的Email已经被另一帐号注册，请使其它帐号！","-1");
			exit();
		}
	}

	//检测用户名是否存在
	$row = $dsql->GetOne("Select mid From `#@__member` where userid like '$userid' ");
	if(is_array($row))
	{
		ShowMsg("你指定的用户名 {$userid} 已存在，请使用别的用户名！","-1");
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
			ShowMsg('你的新安全问题的答案太长了，请控制在30字节以内！','-1');
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
	$inQuery = "INSERT INTO `#@__member` (`mtype` ,`userid` ,`pwd` ,`uname` ,`sex` ,`rank` ,`uprank` ,`money` ,
 	 `upmoney` ,`email` ,`scores` ,`matt` ,`face`,`safequestion`,`safeanswer` ,`jointime` ,`joinip` ,`logintime` ,`loginip` )
   VALUES ('$mtype','$userid','$pwd','$uname','$sex','10','0','$dfmoney','0',
   '$email','$dfscores','0','','$safequestion','$safeanswer','$jointime','$joinip','$logintime','$loginip'); ";
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
		$ml = new MemberLogin(7*3600);
		$rs = $ml->CheckUser($userid,$userpwd);
		ShowMsg("注册成功，3秒钟后转向系统主页...","index.php",0,2000);
		exit();
	}
	else
	{
		ShowMsg("注册失败，请检查资料是否有误或与管理员联系！","-1");
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