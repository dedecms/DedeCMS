<?php
require_once(dirname(__FILE__)."/config.php");

CheckRank(0,0);
setcookie("ENV_GOBACK_URL",GetCurUrl(),time()+3600,"/");
$timestamp = mytime();
$db = new dedesql();
if($cfg_mb_score2money == 'Y'){
	if(empty($action)){
		require_once(dirname(__FILE__)."/../include/pub_datalist_dm.php");
		$member = $db->getone("select ID, userid, type, uname, membertype, money, scores from #@__member
						where ID=$cfg_ml->M_ID limit 1");
		$query = "select * from #@__score2money_logs where uid=$cfg_ml->M_ID order by dateline asc";

		$dlist = new DataList();
		$dlist->Init();
		$dlist->pageSize = 20;
		$dlist->SetSource($query);
		$log = $dlist->GetDataList();
		$logs = array();
		while($row = $log->GetArray('dm')) {
			$row['dbdateline'] = GetDateTimeMk($row['dateline']);
			$row['dbtype'] = $row['type'] == 'score2money' ? '积分兑换金币' : '金币购买积分';
			$logs[] = $row;
		}
		require_once(dirname(__FILE__)."/templets/money2score.htm");
		$dlist->Close();
		$db->Close();
	}elseif($action == 'buy'){
		$money = intval($money);
		if($type == 'money2score' && $money > 0 && $cfg_money2score > 0)
		{
			$score = $cfg_money2score * $money;
			$member = $db->getone("select ID, userid, type, uname, membertype, money, scores from #@__member
							where ID=$cfg_ml->M_ID limit 1");
			if($member['money'] < $money){
				showmsg('您的金币不足','-1');
				$db->Close();
				exit();
			}else{
				$db->setquery("update #@__member set money=money-$money, scores=scores+$score where ID='{$cfg_ml->M_ID}'");
				if($db->executenonequery()){
					$money = -$money;
					$db->setquery("insert into #@__score2money_logs(uid, username, dateline, type, ratio, score, money)
					values ('$member[ID]','$member[userid]','$timestamp','money2score','$cfg_money2score','$score','$money')");
					$db->executenonequery();
				}
			}
			$cfg_ml->FushCache();
			$db->Close();
			showmsg('金币兑换积分成功','money2score.php');
			exit();
		//end money2score
		}elseif($type == 'score2money' && $score > 0 && $cfg_score2money > 0)
		{
			$score = intval($score);
			$money = @floor($score/$cfg_score2money);
			$member = $db->getone("select ID, userid, type, uname, membertype, money, scores from #@__member
							where ID=$cfg_ml->M_ID limit 1");

			if($member['scores'] < $score){
				$db->Close();
				showmsg('您的积分不足','-1');
				exit();
			}else{
				$db->setquery("update #@__member set scores=scores-$score, money=money+$money where ID='{$cfg_ml->M_ID}'");
				if($db->executenonequery()){
					$score = -$score;
					$db->setquery("insert into #@__score2money_logs(uid, username, dateline, type, ratio, score, money)
						values ('$member[ID]','$member[userid]','$timestamp','score2money','$cfg_score2money','$score','$money')");
					$db->executenonequery();
				}
			}
			$cfg_ml->FushCache();
			$db->Close();
			showmsg('积分兑换金币成功','money2score.php');
			exit();
		}//end score2moeny
		$db->Close();
		showmsg('请按说明操作','money2score.php');
		exit();
	}// end buy
}else{
$db->Close();
ShowMsg('系统未开启金币、积分互换功能','-1');
exit();
}

?>