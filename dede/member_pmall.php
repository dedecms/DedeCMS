<?php
require_once(dirname(__FILE__)."/config.php");
CheckPurview('member_Pm');
if(!isset($action)) $action = '';
if($action=="post"){	
	$msgfrom = '管理员';
	$msgfromid = 0;
	$msgtoid = 0;
	$dateline = time();
	$subject = cn_substr(trim(ClearHtml($subject)),70);
	$message = cn_substr(trim(ClearHtml($message)),1000);
	if(!isset($subject)||empty($subject)){
		ShowMsg("短信标题不能为空!","-1");
		exit();
	}else if(!isset($message)||empty($message)){
		ShowMsg("请填写短信内容!","-1");
		exit();
	}
	$db = new DedeSql(false);
	$rs = $db->ExecuteNoneQuery("INSERT INTO #@__pms(msgfrom,msgfromid,msgtoid,folder,new,subject,dateline,message,isadmin) VALUES('$msgfrom','$msgfromid','$msgtoid','inbox',1,'{$subject}','$dateline','{$message}',1);");
	if($rs)
	{
		$db->ExecuteNoneQuery("UPDATE	#@__member SET newpm=newpm+1");		
	}
	ShowMsg("短信已成功发送","-1");
	$db->Close();
	exit();
}
require_once(dirname(__FILE__)."/templets/member_pmall.htm");
?>