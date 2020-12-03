<?php

/**
 * Enter description here...
 *
 * @author Administrator
 * @package defaultPackage
 * @rcsfile 	$RCSfile: question.php,v $
 * @revision 	$Revision: 1.1 $
 * @date 	$Date: 2009/08/04 04:06:21 $
 */

require_once dirname(__FILE__).'/include/common.inc.php';

if($cfg_ask_rewrite == 'Y')
{
	$queryarr = explode('-', $_SERVER['QUERY_STRING']);
	$tmpn = count($queryarr)/2;
	for($tmpi=0; $tmpi<$tmpn;$tmpi++)
	{
		$tmpk = 2 * $tmpi;
		$tmpv = 2 * $tmpi + 1;
		${$queryarr[$tmpk]} = $queryarr[$tmpv];
	}
}
$question = $dsql->getone("SELECT ask.*, mem.userid as username, mem.scores
FROM `#@__ask` ask left join `#@__member` mem on mem.mid=ask.uid WHERE ask.id='$id' and status>='0'");
if($question)
{
	if($question['status'] == 1)
	{
		$question['dbstatus'] = 1;
		$question['status'] = 'qa_ico_4.gif';
	}elseif($question['expiredtime'] < $timestamp)
	{
		$question['dbstatus'] = 3;
		$question['status'] = 'qa_ico_4.gif';
		$dsql->setquery("update #@__ask set solvetime=expiredtime, status='3' where id='$id'");
		$dsql->executenonequery();
		$question['solvetime'] = $question['expiredtime'];
	}elseif($question['status'] == 2)
	{
		$question['dbstatus'] = 2;
		$question['status'] = 'qa_ico_4.gif';
	}else
	{
		$question['dbstatus'] = 0;
		$question['status'] = 'qa_ico_3.gif';
	}
	$question['content'] = nl2br(ihtmlspecialchars($question['content']));
	$question['extra'] = nl2br(ihtmlspecialchars($question['extra']));
	$question['toendsec'] = $question['expiredtime'] - $timestamp;
	$question['toendday'] = floor($question['toendsec']/86400);
	$question['toendhour'] = floor(($question['toendsec']%86400)/3600);
	$question['dateline'] = gmdate('m-d', $question['dateline'] + ($timeoffset * 3600));
	$question['solvetime'] = gmdate('Y-m-d h:i', $question['solvetime'] + ($timeoffset * 3600));
	$publisher = 0;
	if($question['uid'] == $uid)
	{
		$publisher = 1;
	}
}else
{
	showmsgs('question_nonexistence', '-1');
}

//等级
$honors = array();
$dsql->setquery("Select id, titles, icon, integral From `#@__scores` order by integral desc");
$dsql->execute();
while($row = $dsql->getarray())
{
	$honors[] = $row;
}
$question['honor'] = gethonor($question['scores'], $honors);

$nav = '<a href="'.$indexname.'">'.$sitename.'</a> '.$symbols.' <a href="browser.php?tid='.$question['tid'].'">'.$question['tidname'].'</a>';
$navtitle = $question['title'];
if($question['tid2'])
{
	$nav .= ' '.$symbols.' <a href="browser.php?tid2='.$question['tid2'].'">'.$question['tid2name'].'</a>';
	$navtitle .= ' '.$question['tid2name'];
}
$navtitle .= ' '.$question['tidname'].' '.$sitename;

$dsql->Execute('me',"select answer.*,m.scores from #@__askanswer answer left join `#@__member` m on m.mid=answer.uid where askid='$id' and ifcheck='1'");
$comments = $answers = array();
$first = $goodrateper = $badrateper = $goodrate = $badrate = $ratenum = $commentnum = $answernum = $myanswer = 0;
while($row = $dsql->getarray())
{
	$row['dateline'] = gmdate('m-d h:i', $row['dateline'] + ($timeoffset * 3600));
	$row['dbcontent'] = $row['content'];
	$row['content'] = nl2br(ihtmlspecialchars($row['content']));
	$row['honor'] = gethonor($row['scores'], $honors);
	if($row['ifanswer'] == 1)
	{
		//回答
		if($uid == $row['uid'])
		{
			$myanswer = 1;
		}
		if($row['id'] == $question['bestanswer'])
		{
			$digestanswer = $row;
			$ratenum = $row['goodrate'] + $row['badrate'];
			$goodrate = $row['goodrate'];
			$badrate = $row['badrate'];
			$goodrateper = @ceil($goodrate*100/$ratenum);
			$badrateper = 100-$goodrateper;
		}else
		{
			$answers[] = $row;
			$answernum++;
		}
	}
	else
	{
		//对最佳答案的评价
		if($row['uid'] == $question['uid'] && $first == 0)
		{
			$publishercomment = $row;
			$first = 1;
		}else
		{
			$commentnum++;
			$comments[] = $row;
		}
	}
}

//快到期的问题
$query = "select id, tid, tidname, tid2, tid2name, title from `#@__ask` where status='0' order by expiredtime asc, dateline desc limit 10";
$dsql->Execute('me',$query);
$expiredasks = array();
while($row = $dsql->getarray())
{
	$row['title'] = cn_substr($row['title'],24);
	$expiredasks[] = $row;
}

//会员排行
$query = "select mid as ID, userid, scores from `#@__member` order by scores desc limit 10";
$dsql->Execute('me',$query);
$topmembers = array();
while($row = $dsql->getarray())
{
	$topmembers[] = $row;
}

$dtp = new DedeTemplate();
$dtp->LoadTemplate(DEDEASK.'template/default/question.htm');
if($cfg_ask_rewrite=='Y')
{
	$dtp->Display();
	myecho();
	exit();
}
else
{
	$dtp->Display();
}
?>