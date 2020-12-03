<?php
require_once(dirname(__FILE__)."/config.php");
require_once(dirname(__FILE__)."/../include/pub_datalist_dm.php");
CheckRank(0,0);
if(!isset($action)) $action = '';
if(!isset($deletefriend)) $deletefriend = false;

$db = new DedeSql(false);
$action = ereg_replace("[^a-z]","",$action);
//删除好友
if($deletefriend==true && !empty($delete)){
	foreach($delete as $key=>$val){
		$db->ExecuteNoneQuery("DELETE FROM #@__friends WHERE friend_id='$val'");
	}
}
//添加好友
if($action=="add"){
	$uid = ereg_replace("[^0-9]","",$uid);
	$row = $db->GetOne("SELECT friend_id FROM #@__friends WHERE friend_from='$uid' AND friend_to='".$cfg_ml->M_ID."'");
	if(is_array($row)){
		$db->Close();
		ShowMsg("他(她)已经是您的好友！","-1");
		exit();
	}else{
		$subject = "会员".$cfg_ml->M_UserName."已加您为好友!";
		$members = $db->GetOne("SELECT uname FROM #@__member WHERE ID='$uid'");
		if(!is_array($members)){
			$db->Close();
			ShowMsg("您要加的好成不存在,或已被删除！","-1");
			exit();
		}
		$message = "嗨,".$members['uname'].":会员<font color=green>".$cfg_ml->M_UserName."</font>";
		if(!isset($do)){
			$message .= "已加您为好友!<br>\n--><a href=\"my_friends.php?uid=".$cfg_ml->M_ID."&action=add&do=pass\" style=\"color:red\">通过,并加为好友</a>";
		}else if($do=="pass"){
			$message .= "已通过你的好友邀请!";
		}
		$db->ExecuteNoneQuery("INSERT INTO #@__pms(msgfrom,msgfromid,msgtoid,folder,new,subject,dateline,message) VALUES('".$cfg_ml->M_UserName."','".$cfg_ml->M_ID."','".$uid."','inbox',1,'{$subject}','".time()."','".$message."');");
		echo $db->GetError();
		$db->ExecuteNoneQuery("INSERT INTO #@__friends(friend_from,friend_to) VALUES('".$uid."','".$cfg_ml->M_ID."');");
		$db->Close();
		ShowMsg("<font color=green>成功加".$members['uname']."为好友!</font>","-1");
		exit();
	}
}
$db->Close();
$query = "SELECT friend_id,friend_from,uname,userid FROM #@__friends LEFT JOIN #@__member ON ID=friend_from WHERE friend_to='".$cfg_ml->M_ID."' ORDER BY friend_id DESC";
$dbpage = new DataList();
$dbpage->pageSize = 20;
$dbpage->SetSource($query);
require_once(dirname(__FILE__)."/templets/my_friends.htm");
$dbpage->Close();
?>