<?php
require_once(dirname(__FILE__)."/config.php");
CheckRank(0,0);
require_once(DEDEINC."/enums.func.php");

if(!isset($dopost))
{
	$dopost = '';
}
if($dopost=='')
{
	if($cfg_ml->M_MbType=='个人')
	{
		$row = $dsql->GetOne("select  * from `#@__member_person` where mid='".$cfg_ml->M_ID."'");
		if(!is_array($row))
		{
			$inquery = "INSERT INTO `#@__member_person` (`mid` , `onlynet` , `sex` , `uname` , `qq` , `msn` , `tel` , `mobile` , `place` , `oldplace` ,
	           `birthday` , `star` , `income` , `education` , `height` , `bodytype` , `blood` , `vocation` , `smoke` , `marital` , `house` ,
	            `drink` , `datingtype` , `language` , `nature` , `lovemsg` , `address`,`uptime`)
             VALUES ('{$cfg_ml->M_ID}', '1', '{$cfg_ml->fields['sex']}', '{$cfg_ml->fields['uname']}', '', '', '', '', '0', '0',
              '1980-01-01', '1', '0', '0', '160', '0', '0', '0', '0', '0', '0','0', '0', '', '', '', '','0'); ";
			$dsql->ExecuteNoneQuery($inquery);
			$row = $dsql->GetOne("select  * from `#@__member_person` where mid='".$cfg_ml->M_ID."'");
			if(!is_array($row))
			{
				ShowMsg("系统出错，请联系管理员！","-1");
				exit();
			}
			$dsql->ExecuteNoneQuery("update `#@__member` set spacesta=2 where mid='{$cfg_ml->M_ID}' ");
		}
		include(DEDEMEMBER."/templets/edit_info_person.htm");
	}
	elseif($cfg_ml->M_MbType=='企业')
	{
		$row = $dsql->GetOne("select  * from `#@__member_company` where mid='".$cfg_ml->M_ID."'");
		if(!is_array($row))
		{
			$uptime = time();
			$inquery = "INSERT INTO `#@__member_company` (`mid`,`company`,`product`,`place`,`vocation`,`cosize`,`tel`,`fax`,`linkman`,`address`,`mobile`,`email`,`url`,`uptime`,`checked`,`introduce`)
                VALUES ('{$cfg_ml->M_ID}','{$cfg_ml->fields['uname']}','product','0','0','0','','','','','','{$cfg_ml->fields['email']}','','$uptime','0',''); ";
			$dsql->ExecuteNoneQuery($inquery);
			$row = $dsql->GetOne("select  * from `#@__member_company` where mid='".$cfg_ml->M_ID."'");
			if(!is_array($row))
			{
				ShowMsg("系统出错，请联系管理员！","-1");
				exit();
			}
			$dsql->ExecuteNoneQuery("update `#@__member` set spacesta=2 where mid='{$cfg_ml->M_ID}' ");
		}
		include(DEDEMEMBER."/templets/edit_info_company.htm");
	}
}

/*------------------------
function __Save()
------------------------*/
if($dopost=='save')
{
	if($cfg_ml->M_MbType=='个人')
	{
		if(empty($city))
		{
			$place = $province;
		}
		else
		{
			$place = $city;
		}
		if(empty($oldcity))
		{
			$oldplace = $oldprovince;
		}
		else
		{
			$oldplace = $oldcity;
		}
		if(!isset($nature))
		{
			$tnature = '';
		}
		else
		{
			$tnature = join(',',$nature);
		}
		if(!isset($language))
		{
			$tlanguage = '';
		}
		else
		{
			$tlanguage = join(',',$language);
		}
		if($birthday=='')
		{
			$birthday = '1980-01-01';
		}
		
		$uname = HtmlReplace($uname,2);
		$uname = HtmlReplace($uname,2);
		$qq = GetAlabNum($qq);		
		$msn = HtmlReplace($msn,2);
		$tel = GetAlabNum($tel);
		$mobile = GetAlabNum($mobile);
		$lovemsg = cn_substrR(HtmlReplace($lovemsg,0),100);
		$address = cn_substrR(HtmlReplace($address,1),50);
		$uptime = time();
		
		$row = $dsql->GetOne("SELECT `sex` FROM `#@__member_person` WHERE `mid`='".$cfg_ml->M_ID."'");
		$sex = isset($row['sex']) && !empty($row['sex']) ? $row['sex'] : '保密';
		
		$dsql->ExecuteNoneQuery("Delete From `#@__member_person` where mid='{$cfg_ml->M_ID}' ");
		$inquery = "Insert Into `#@__member_person`(`mid` ,`onlynet` ,`sex` ,`uname` ,`qq` ,`msn` ,`tel` ,`mobile` ,`place` ,`oldplace` ,
	              `birthday` ,`star` ,`income` ,`education` ,`height` , `bodytype` , `blood` , `vocation` , `smoke` ,
	               `marital` , `house` ,`drink` , `datingtype` , `language` , `nature` , `lovemsg` ,`uptime`, `address`)
                VALUES ('{$cfg_ml->M_ID}', '$onlynet', '$sex', '{$cfg_ml->fields['uname']}', '$qq', '$msn', '$tel', '$mobile', '$place', '$oldplace',
              '$birthday', '$star', '$income', '$education', '$height', '$bodytype', '$blood', '$vocation', '$smoke',
               '$marital' , '$house' ,'$drink' , '$datingtype' , '$tlanguage' , '$tnature' , '$lovemsg' ,'$uptime', '$address'); ";
		$rs = $dsql->ExecuteNoneQuery($inquery);
		if(!$rs)
		{
			ShowMsg("保存信息时发生错误，请联系管理员！".$dsql->GetError(),'javascript:;');
			exit();
		}
		$dsql->ExecuteNoneQuery("update `#@__member` set spacesta=2 where mid='{$cfg_ml->M_ID}' ");
		ShowMsg("成功修改你的资料！",'edit_fullinfo.php');
		exit();
	}
	else if($cfg_ml->M_MbType=='企业')
	{
		$userdir = $cfg_user_dir.'/'.$cfg_ml->M_ID;
		if(!ereg('^'.$userdir,$oldcomface))
		{
			$oldcomface = '';
		}
		if(is_uploaded_file($comface))
		{
			//删除旧图片（防止文件扩展名不同，如：原来的是gif，后来的是jpg）
			if($oldcomface!='' && file_exists($cfg_basedir.$oldcomface))
			{
				@unlink($cfg_basedir.$oldcomface);
			}
			//上传新工图片
			$comface = MemberUploads('comface','',$cfg_ml->M_ID,'image','comface',200,80);
		}
		else
		{
			$comface = $oldcomface;
		}



		if(empty($city))
		{
			$place = $province;
		}
		else
		{
			$place = $city;
		}
		$tel = GetAlabNum($tel);
		$fax = GetAlabNum($fax);
		$mobile = GetAlabNum($mobile);
		$email = cn_substrR(eregi_replace("[^0-9a-z\.@-]",'',$email),50);
		$url = cn_substrR(eregi_replace("[^0-9a-z\.:/-]",'',$url),50);
		$product = cn_substrR(HtmlReplace($product,1),20);
		$linkman = cn_substrR(HtmlReplace($linkman,1),20);
		$company = cn_substrR(HtmlReplace($company,1),36);
		$address = cn_substrR(HtmlReplace($address,1),50);
		$introduce = HtmlReplace($introduce,-1);
		$uptime = time();
		$dsql->ExecuteNoneQuery("Delete From `#@__member_company` where mid='{$cfg_ml->M_ID}' ");
		$inquery = "INSERT INTO `#@__member_company` (`mid` , `company` , `product` , `place` , `vocation` , `cosize` , `tel` , `fax` , `linkman` , `address`,`uptime` ,`mobile`,`email`,`url`, `introduce` ,`comface`)
                VALUES ('{$cfg_ml->M_ID}','$company','$product','$place','$vocation','$cosize','$tel','$fax','$linkman','$address','$uptime','$mobile','$email','$url','$introduce', '$comface'); ";
		$rs = $dsql->ExecuteNoneQuery($inquery);
		if(!$rs)
		{
			ShowMsg("保存信息时发生错误，请联系管理员！".$dsql->GetError(),'javascript:;');
			exit();
		}
		$dsql->ExecuteNoneQuery("update `#@__member` set spacesta=2 where mid='{$cfg_ml->M_ID}' ");
		ShowMsg("成功修改你的企业资料！",'edit_fullinfo.php');
	}
}
?>