<?php

$cfg_needFilter = TRUE;

require_once dirname(__FILE__).'/include/common.inc.php';
require_once(DEDEINC.'/filter.inc.php');

if(!isset($action)) $action = '';
if(empty($uid) && $action != 'rate')
{
	showmsgs('no_login','../member/login.php?gourl=../ask/');
	exit;
}

//词语过滤也应该在此处处理
$title = isset($title) ? ihtmlspecialchars(trim($title)) : '';
$brief = isset($brief) ? ihtmlspecialchars(trim($brief)) : '';
$content = isset($content) ? trim($content) : '';
$extra = isset($extra) ? trim($extra) : '';

if(empty($action))
{
	//问答分类
	$query = "select id, name, reid from `#@__asktype` order by disorder desc";
	$dsql->Execute('me',$query);
	$tids = "var class_level_1=new Array( \n";
	$tid2s = "var class_level_2=new Array( \n";
	while($asktype = $dsql->getarray())
	{
		if($asktype['reid'] == 0)
		{
			$tids .= 'new Array("'.$asktype['id'].'","'.$asktype['name'].'"),'."\n";
		}else
		{
			$tid2s .= 'new Array("'.$asktype['reid'].'","'.$asktype['id'].'","'.$asktype['name'].'"),'."\n";
		}
	}
	$tids = substr($tids,0,-2)."\n";
	$tid2s = substr($tid2s,0,-2)."\n";
	$tids .= ');';
	$tid2s .= ');';

	$navtitle = $sitename.' 提问';
	$nav = '<a href="'.$indexname.'">'.$sitename.'</a> '.$symbols.' 提问';
	$dtp = new DedeTemplate();
	$dtp->LoadTemplate(DEDEASK.'template/default/post.htm');
	$dtp->Display();
}

/*-----------------------
function extra();
补充问题
-----------------------*/
else if($action == 'extra')
{
	$dsql->Execute('me',"SELECT id, uid, dateline, expiredtime, solvetime, extra FROM `#@__ask` WHERE id='$id' and status='0' ");

	if($question = $dsql->getarray())
	{
		if($question['uid'] != $uid)
		{
			showmsgs('unallowed_action');
		}elseif($question['expiredtime'] < $timestamp)
		{
			showmsgs('question_expired');
		}
	} else
	{
		showmsgs('question_nonexistence');
	}
	if(!empty($step))
	{
		if(empty($extra))
		{
			showmsgs('post_extra_isnull');
		}
		if(strlen($extra) > 10000)
		{
			showmsgs('post_extra_toolong');
		}
		$dsql->ExecuteNoneQuery("update `#@__ask` set extra='$extra' where id='$id' ");
		$dsql->Close();
		@iheader("Expires: 0");
		@iheader("Cache-Control: private, post-check=0, pre-check=0, max-age=0", FALSE);
		@iheader("Pragma: no-cache");
		echo "<script language='javascript'> window.opener.location.reload(); self.close(); </script>";
		exit();
	}
	else
	{
		include DEDEASK.'template/default/post.extra.htm';
		exit();
	}
}

/*-----------------------
function upreward();
//提高悬赏
-----------------*/
else if($action == 'upreward')
{
	$question = $dsql->getone("SELECT id, uid, dateline, solvetime, status, expiredtime FROM `#@__ask` WHERE id='$id' and status='0'");
	if($question)
	{
		if($question['uid'] != $uid)
		{
			showmsgs('unallowed_action');
		}elseif($question['expiredtime'] < $timestamp)
		{
			showmsgs('question_expired');
		}
	} else
	{
		showmsgs('question_nonexistence');
	}

	if(empty($step))
	{
		include DEDEASK.'template/default/post.upreward.htm';
		exit();
	}else{
		$upreward = intval($upreward);
		$upreward = max(0,$upreward);

		if($upreward > $scores)
		{
			showmsgs('noscore');
		}

		$dsql->ExecuteNoneQuery("UPDATE `#@__member` SET scores=scores-$upreward WHERE mid='$uid'");
		$dsql->ExecuteNoneQuery("update `#@__ask` set reward=reward+$upreward where id='$id'");
		@iheader("Expires: 0");
		@iheader("Cache-Control: private, post-check=0, pre-check=0, max-age=0", FALSE);
		@iheader("Pragma: no-cache");
		echo "<script language='javascript'> window.opener.location.reload(); self.close(); </script>";
		exit();

	}
}
/*------------------------
function modifyanswer();
修改答案
--------------------*/
else if($action == 'modifyanswer')
{
	$answer = $dsql->getone("SELECT answer.id, answer.uid, ask.dateline, ask.solvetime, ask.status, ask.expiredtime FROM `#@__askanswer` answer left join #@__ask ask on ask.id=answer.askid WHERE answer.id='$id'");
	if($answer)
	{
		if($answer['uid'] != $uid)
		{
			showmsgs('unallowed_action', $backurl);
		}elseif($answer['status'] != 0)
		{
			showmsgs('question_solved', $backurl);
		}elseif($answer['expiredtime'] < $timestamp)
		{
			showmsgs('question_expired', $backurl);
		}
	} else
	{
		showmsgs('question_nonexistence');
	}

	if(trim($content) == '')
	{
		showmsgs('post_answer_isnull');
	}
	if(strlen($content) > 10000)
	{
		showmsgs('post_answer_toolong');
	}
	if(strlen($brief) > 200)
	{
		showmsgs('post_brief_toolong', '-1');
	}
	if($dsql->ExecuteNoneQuery("update #@__askanswer set content='$content', brief='$brief' where id='$id'"))
	{
		showmsgs('modifyanswer_success',$backurl);
	}else
	{
		showmsgs('modifyanswer_failed',$backurl);
	}
}

/*------------------
function adopt();
采纳答案
-------------*/
else if($action == 'adopt')
{
	if(empty($step))
	{
		$step = 0;
	}
	$answer = $dsql->getone("SELECT answer.id, answer.askid, answer.uid as answeruid, ask.uid, ask.tid, ask.tid2,
	ask.reward, ask.dateline, ask.solvetime, ask.status, ask.expiredtime
	FROM `#@__askanswer` answer left join `#@__ask` ask on ask.id=answer.askid WHERE answer.id='$id'");
	if($step != 2)
	{
		if($answer)
		{
			if($answer['uid'] != $uid)
			{
				showmsgs('unallowed_action', $backurl);
			}elseif($answer['status'] != 0)
			{
				showmsgs('question_solved', $backurl);
			}elseif($answer['expiredtime'] < $timestamp)
			{
				showmsgs('question_expired', $backurl);
			}
		} else
		{
			showmsgs('question_nonexistence');
		}
		include DEDEASK.'template/default/post.adopt.htm';
		exit();
	}else
	{
		$extrareward = intval($extrareward);
		$extrareward = max(0,$extrareward);
		$content = trim($content);
		if($extrareward > $scores)
		{
			showmsgs('noscore');
		}
		$reward = $extrareward + $answer['reward'] + $cfg_ask_bestanswer;//额外奖励+提问奖励+系统奖励
		$dsql->ExecuteNoneQuery("UPDATE `#@__member` SET scores=scores+$reward WHERE mid='{$answer['answeruid']}' ");
		$dsql->ExecuteNoneQuery("update `#@__ask` set solvetime='$timestamp', status='1', bestanswer='{$answer['id']}' where id='{$answer['askid']}' ");
		$userip = getip();
		if($content != '')
		{
			//写入感谢语
			$dsql->ExecuteNoneQuery("insert into #@__askanswer (askid, ifanswer, tid, tid2, uid, username, userip, dateline, content)
			values('$answer[askid]', '0', '$answer[tid]', '$answer[tid2]', '$uid', '$username', '$userip', '$timestamp', '$content')");
		}
		@iheader("Expires: 0");
		@iheader("Cache-Control: private, post-check=0, pre-check=0, max-age=0", FALSE);
		@iheader("Pragma: no-cache");
		echo "<script language='javascript'> window.opener.location.reload(); self.close(); </script> ";
		exit();
	}
}

/*------------------------
function comment();
//对最佳答案的评论
------------------------*/
else if($action == 'comment')
{
	if(trim($content) == '')
	{
		showmsgs('post_comment_isnull');
	}
	if(strlen($content) > 10000)
	{
		showmsgs('post_comment_toolong');
	}
	$digestanswer = $dsql->getone("select answer.askid, answer.tid, answer.tid2, ask.bestanswer
	from `#@__askanswer` answer left join `#@__ask` ask on ask.id=answer.askid where answer.id='$id'");
	if($digestanswer && $digestanswer['bestanswer'] == $id)
	{
		$tid = $digestanswer['tid'];
		$askid = $digestanswer['askid'];
		$tid2 = $digestanswer['tid2'];
		$userip = getip();
	}else
	{
		showmsgs('unallowed_action');
	}
	$dsql->ExecuteNoneQuery("insert into `#@__askanswer`(askid, ifanswer, tid, tid2, uid, username, userip, dateline, content)
	values('$askid', '0', '$tid', '$tid2', '$uid', '$username', '$userip', '$timestamp', '$content')");

	showmsgs('post_comment_succeed',"question.php?id=$askid");
}

/*-----------------------
function rate();
对最佳答案的评价
------------------*/
else if($action == 'rate')
{
	if($type == 'bad')
	{
		$rate = 'badrate';
	}else
	{
		$rate = 'goodrate';
	}
	$cookiename = 'rated'.$id;

	if(!isset(${$cookiename}))
	{
		${$cookiename} = 0;
	}
	if((!${$cookiename} == $id))
	{
		$dsql->ExecuteNoneQuery("update `#@__askanswer` set $rate=$rate+1 where id='$id'");
		makecookie($cookiename,$id,3600);
	}
	$row = $dsql->getone("select goodrate, badrate from `#@__askanswer` where id='$id'");

	$goodrate = $row['goodrate'];
	$badrate = $row['badrate'];
	if(($goodrate + $badrate) > 0)
	{
		$goodrateper = ceil($goodrate*100/($badrate+$goodrate));
		$badrateper = 100-$goodrateper;
	}else
	{
		$goodrateper = $badrateper = 0;
	}
	AjaxHead();
?>
				<dl>
					<dt><strong>您觉得最佳答案好不好？ </strong><br>   目前有 <?php echo $row['goodrate']+$row['badrate']; ?> 个人评价</dt>
					<dd>
						<a href="#"  onCLick="rate('mark',<?php echo $id; ?>,'good')"><img src="template/default/images/mark_g.gif" width="14" height="16" />好</a>
						<span><?php echo $goodrateper; ?>% (<?php echo $goodrate; ?>)</span>
					</dd>
					<dd>
						<a href="#"  onCLick="rate('mark',<?php echo $id; ?>,'bad')"><img src="template/default/images/mark_b.gif" width="14" height="16" />不好</a>
						<span><?php echo $badrateper; ?>% (<?php echo $badrate; ?>)</span>
					</dd>
				</dl>
<?php
}

/*----------------
function ask();
提问问题
--------------*/
else if($action == 'ask')
{
	if($title == '')
	{
		showmsgs('post_title_isnull');
	}

	if(strlen($title) > 80)
	{
		return 'post_title_toolong';
	}
	if(strlen($content) > 10000)
	{
		showmsgs('post_askcontent_toolong');
	}
	$anonymous = !empty($anonymous) ? 1 : 0;
	$tid = $tid2 = 0;
	$tidname = $tid2name = '';
	$userip = getip();
	$reward = intval($reward);
	if($reward < 0)
	{
		$reward = 0;
	}
	$needscore = $anonymous * 10 + $reward;
	if($scores < $needscore)
	{
		showmsgs('noscore','-1');
	}
	$ClassLevel1 = intval($ClassLevel1);
	if($ClassLevel1 < 1)
	{
		showmsgs('browser_error','-1');
	}
	$ClassLevel2 = intval($ClassLevel2);
	if($ClassLevel2 != 0)
	{
		$where = "id in ($ClassLevel1,$ClassLevel2)";
	}else
	{
		$where = "id='$ClassLevel1'";
	}
	$query = "select id, name, reid from `#@__asktype` where $where";

	$dsql->Execute('me',$query);
	while($row = $dsql->getarray())
	{
		if($row['id'] == $ClassLevel1)
		{
			$tidname = $row['name'];
			$tid = $row['id'];
		}elseif($row['id'] == $ClassLevel2 && $row['reid'] == $ClassLevel1)
		{
			$tid2name = $row['name'];
			$tid2 = $row['id'];
		}
	}
	$expiredtime = $timestamp + 86400 * $cfg_ask_expiredtime;
	if($cfg_ask_ifcheck == 'Y')
	{
		$dsql->ExecuteNoneQuery("insert into `#@__ask`(tid, tidname, tid2, tid2name, uid, anonymous, status, title, reward, dateline, expiredtime, ip ,content, extra) values ('$tid', '$tidname', '$tid2', '$tid2name', '$uid', '$anonymous', '-1', '$title', '$reward', '$timestamp', '$expiredtime', '$userip', '$content', '')");
	}else
	{
		$dsql->ExecuteNoneQuery("insert into `#@__ask`(tid, tidname, tid2, tid2name, uid, anonymous,
								title, reward, dateline, expiredtime, ip ,content, extra)
						values('$tid', '$tidname', '$tid2', '$tid2name', '$uid', '$anonymous', '$title', '$reward',
							'$timestamp', '$expiredtime', '$userip', '$content', '')");
	}
	$dsql->ExecuteNoneQuery("UPDATE `#@__asktype` SET asknum=asknum+1 WHERE id='$tid'");
	if($tid2 > 0)
	{
		$dsql->ExecuteNoneQuery("UPDATE `#@__asktype` SET asknum=asknum+1 WHERE id='$tid2'");
	}
	$dsql->ExecuteNoneQuery("UPDATE `#@__member` SET scores=scores-$needscore WHERE mid='$uid'");
	showmsgs('post_newask_succeed', "browser.php?tid=$tid");
}

/*------------------
function answer();
回答问题
-------------------*/
else if($action == 'answer')
{
	$dsql->Execute('me',"SELECT id, tid, tid2, uid, dateline, expiredtime, solvetime FROM `#@__ask` WHERE id='$id' and status='0'");

	if($question = $dsql->getarray())
	{
		if($question['uid'] == $uid)
		{
			showmsgs('asker_cannot_answer', '-1');
		}elseif($question['expiredtime'] < $timestamp)
		{
			showmsgs('question_expired');
		}
		$tid = $question['tid'];
		$tid2 = $question['tid2'];
		$askid = $question['id'];
		$userip = getip();
	} else
	{
		showmsgs('question_nonexistence');
	}

	$anonymous = isset($anonymous) ? 1 : 0;

	if($content == '')
	{
		showmsgs('post_answer_isnull');
	}
	if(strlen($content) > 10000)
	{
		showmsgs('post_answer_toolong');
	}
	if(strlen($brief) > 200)
	{
		showmsgs('post_brief_toolong', '-1');
	}
	if($cfg_ask_ifcheck == 'Y')
	{
		$dsql->ExecuteNoneQuery("insert into `#@__askanswer` (askid, ifanswer, tid, tid2, uid, username, anonymous, userip, brief, dateline, content, ifcheck)
			values('$askid', '1', '$tid', '$tid2', '$uid', '$username', '$anonymous', '$userip', '$brief', '$timestamp', '$content', '0')");
	}
	else
	{
		$dsql->ExecuteNoneQuery("insert into `#@__askanswer` (askid, ifanswer, tid, tid2, uid, username, anonymous, userip, brief, dateline, content)
			values('$askid', '1', '$tid', '$tid2', '$uid', '$username', '$anonymous', '$userip', '$brief', '$timestamp', '$content')");
	}
	$dsql->ExecuteNoneQuery("update `#@__ask` set replies=replies+1 where id='$askid' ");
	$cfg_ask_answerscore = intval($cfg_ask_answerscore);
	$dsql->ExecuteNoneQuery("update `#@__member` set scores=scores+{$cfg_ask_answerscore} where mid='$uid' "); //回答者先加分
	showmsgs('post_answer_succeed',"question.php?id=$askid");
}

/*---------------------
function toend();
无满意答案，结束问题
----------------*/
else if($action == 'toend')
{
	$dsql->ExecuteNoneQuery("update `#@__ask` set solvetime='$timestamp', status='1' where uid='$uid' and id='$id' ");
	@iheader("Expires: 0");
	@iheader("Cache-Control: private, post-check=0, pre-check=0, max-age=0", FALSE);
	@iheader("Pragma: no-cache");
	echo "<script language='javascript'> window.opener.location.reload(); self.close(); </script>";
	exit();
}


?>