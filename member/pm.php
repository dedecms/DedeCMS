<?php 
require_once(dirname(__FILE__)."/config.php");
require_once(dirname(__FILE__)."/../include/pub_datalist_dm.php");
CheckRank(0,0);
$db = new DedeSql(false);
if(!isset($pmsend)) $pmsend = false;
if($pmsend==true&&!empty($delete)){
	foreach($delete as $key=>$val){
		$db->ExecuteNoneQuery("UPDATE #@__pms SET delstatus='1',`new`='0' WHERE pmid='$val'");		
	}
	$row = $db->GetOne("Select COUNT(*) AS c From #@__pms where msgtoid='".$cfg_ml->M_ID."' AND folder='inbox' AND `new`='1'");		
	$db->ExecuteNoneQuery("UPDATE #@__member SET newpm='".$row['c']."' WHERE ID='".$cfg_ml->M_ID."'");
	$cfg_ml->FushCache();
}
if(isset($action)){
	if($action=="send"){
		if(!isset($vwriter)) $vwriter = '';
		$vwriter = urldecode($vwriter);
		require_once(dirname(__FILE__)."/templets/pm_send.htm");
	}else if($action=="sent"){
		if(!isset($tooutbox)) $tooutbox = '';
		$tooutbox = ereg_replace("[^0-1]","",$tooutbox);
		if(!isset($tooutbox)) $tooutbox = 0;
		if(empty($msgtoid)){
			ShowMsg("发送目标不明确!","-1");
			exit();
		}
		if(empty($subject)){
			ShowMsg("错误!标题不能为空.","-1");
			exit();
		}
		if(strlen($message) < 4){
			ShowMsg("信息内容过少!.","-1");
			exit();
		}
		if(ereg("$cfg_notallowstr",$subject)||ereg("$cfg_notallowstr",$message)){
			ShowMsg("含有非法字符!.","-1");
			exit();
		}
		$subject = preg_replace("/$cfg_replacestr/","***",$subject);
		$message = preg_replace("/$cfg_replacestr/","***",$message);
		$row = $db->GetOne("Select ID,userid From #@__member where userid='$msgtoid' ");
		if(isset($row['ID'])){
			$msgtoid = $row['ID'];					//目的用户ID
			$msgfrom = $cfg_ml->M_LoginID; //发送人用户名
			$msgfromid = $cfg_ml->M_ID;
			$subject = cn_substr(trim(ClearHtml($subject)),70);
			$message = cn_substr(trim(ClearHtml($message)),1000);
			//如果发送到草稿箱
			if($tooutbox) $db->ExecuteNoneQuery("INSERT INTO #@__pms(msgfrom,msgfromid,msgtoid,folder,new,subject,dateline,message) VALUES('".$row['userid']."','$msgfromid','$msgtoid','outbox',1,'{$subject}','".time()."','{$message}');");

			//给对方一份
			$db->ExecuteNoneQuery("INSERT INTO #@__pms(msgfrom,msgfromid,msgtoid,folder,new,subject,dateline,message) VALUES('$msgfrom','$msgfromid','$msgtoid','inbox',1,'{$subject}','".time()."','{$message}');");
			//给自己一份在已发邮件中
			$db->ExecuteNoneQuery("INSERT INTO #@__pms(msgfrom,msgfromid,msgtoid,folder,new,subject,dateline,message) VALUES('".$row['userid']."','$msgfromid','$msgtoid','track',1,'{$subject}','".time()."','{$message}');");
			//更新目标用户新邮件数目
			$row = $db->GetOne("Select COUNT(*) AS c From #@__pms where msgtoid='$msgtoid' AND folder='inbox' AND `new`='1'");
			if($row['c'] > 0){
				$db->ExecuteNoneQuery("UPDATE #@__member SET newpm='".$row['c']."' WHERE ID='{$msgtoid}'");
				//更新对方缓存
			  $cfg_ml->FushCache($msgtoid);
			}
			ShowMsg("短信已成功发送","-1");
			$db->Close();
			exit();
		}else{
			ShowMsg("出错!找不到发送目标.","-1");
			exit();
		}		
		//发送短信中...
	}else if($action=="view"){
		$id = ereg_replace("[^0-9]","",$id);
		if($id<1){
			ShowMsg("短信息不存在!","-1");
			exit();
		}
		$db->ExecuteNoneQuery("UPDATE #@__pms SET new='0' WHERE pmid='$id'");
		$row = $db->GetOne("Select pmid,msgfrom,subject,new,message,dateline,folder From #@__pms where delstatus='0' And pmid='$id' And ( msgtoid='".$cfg_ml->M_ID."' Or msgfromid='".$cfg_ml->M_ID."' ) order by dateline desc");
		if(!isset($row['subject'])){
			ShowMsg("短信息不存在!","-1");
			exit();
		}
		$stime = date('y-m-d h:i:s',$row['dateline']);//短信时间
		$title = $row['subject'];											//短信标题
		$message = $row['message'];										//短信内容
		$msgfrom = $row['msgfrom'];										//短信发送者
		$touser  = "发送到";
		if($row['folder']=="inbox") $touser = "发送者";
		$row = $db->GetOne("Select COUNT(*) AS c From #@__pms where msgtoid='".$cfg_ml->M_ID."' AND folder='inbox' AND `new`='1'");		
		$db->ExecuteNoneQuery("UPDATE #@__member SET newpm='".$row['c']."' WHERE ID='".$cfg_ml->M_ID."'");
		$cfg_ml->FushCache();
		require_once(dirname(__FILE__)."/templets/pm_view.htm");
	}
	$db->Close();
	exit();
}
//收到的最新邮件
$row = $db->GetOne("Select Count(*) as c From #@__pms Where delstatus='0' And msgtoid='".$cfg_ml->M_ID."' And new='1' And folder='inbox'");
$NewMessages = $row['c'];
if(!isset($folder)) $folder = "";
$ar_folder = array('outbox','inbox','track');
//建立folder查询
if(!empty($folder)&&in_array($folder,$ar_folder)){
	if($folder == "outbox"){
		$folders = "And msgfromid='".$cfg_ml->M_ID."' And folder='outbox'";//草稿箱
		$title   = "草稿箱";
		$touser  = "发送到";
	}
	if($folder == "inbox"){
		$folders = "And msgtoid='".$cfg_ml->M_ID."' And folder='inbox'"; //收件箱
		$title   = "收件箱(".$NewMessages.")";
		$touser  = "来自";
	}
	if($folder == "track"){
		$folders = "And msgfromid='".$cfg_ml->M_ID."' And folder='track'"; //已发信
		$title   = "已发信";
		$touser  = "发送到";
	}
}else{
	$folders = "And msgtoid='".$cfg_ml->M_ID."' And folder='inbox'"; //收件箱
	$title   = "收件箱(".$NewMessages.")";
	$touser  = "来自";
}
//如果后台发来的短信息
$db->SetQuery("SELECT `pmid`,`isadmin`,`subject`,`dateline`,`message`  FROM `#@__pms` WHERE `isadmin`>0 AND `new`>0");
$db->Execute();
while($row = $db->GetArray()){
	$msgfrom = '管理员';
	$msgfromid = $row['pmid'];
	$msgtoid = $cfg_ml->M_ID;
	$subject = $row['subject'];
	$dateline = $row['dateline'];
	$message = $row['message'];
	$res = $db->GetOne("SELECT COUNT(*) AS c FROM #@__pms WHERE msgtoid='".$cfg_ml->M_ID."' AND msgfromid='".$row['pmid']."'");
	if($res['c'] < 1){
		$db->ExecuteNoneQuery("INSERT INTO #@__pms(msgfrom,msgfromid,msgtoid,folder,new,subject,dateline,message) VALUES('$msgfrom','$msgfromid','$msgtoid','inbox',1,'{$subject}','$dateline','{$message}');");
		$rs = $db->GetOne("SELECT COUNT(*) AS c FROM #@__pms WHERE msgtoid='".$cfg_ml->M_ID."' AND folder='inbox' AND `new`='1'");		
		$db->ExecuteNoneQuery("UPDATE #@__member SET newpm='".$rs['c']."' WHERE ID='".$cfg_ml->M_ID."'");
		$cfg_ml->FushCache();
		unset($row,$rs);
	}else{
		unset($res);
	}
}
//最后关闭连接
$db->Close();
$query = "Select pmid,msgfrom,msgfromid,subject,new,message,dateline From #@__pms where delstatus='0' ".$folders." And isadmin='0' order by dateline desc";
$dbpage = new DataList();
$dbpage->pageSize = 20;
$dbpage->SetParameter("folder",$folder);
$dbpage->SetSource($query);
require_once(dirname(__FILE__)."/templets/pm.htm");
$dbpage->Close();
?>