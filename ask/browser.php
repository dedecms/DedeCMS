<?php

/**
 * Enter description here...
 *
 * @author Administrator
 * @package defaultPackage
 * @rcsfile 	$RCSfile: browser.php,v $
 * @revision 	$Revision: 1.1 $
 * @date 	$Date: 2009/08/04 04:06:20 $
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

if($tid == 0 && $tid2 == 0 && $lm == 0 )
{
	showmsgs('browser_error','index.php');
	exit;
}

$nav = $navtitle = $multistr = $wheresql = '';

if($tid)
{
	$dsql->Execute('me',"select * from `#@__asktype` where id='$tid' ");
	if(!$typeinfo = $dsql->getarray())
	{
		showmsgs('browser_notexists','index.php');
		exit;
	}
	$wheresql .= " tid='$tid' ";
	$multistr .="tid=$tid";
	$tidstr = "tid=$tid";

	$navtitle = $typeinfo['name'];
	$nav = " $symbols <a href=\"browser.php?tid=$tid\">".$typeinfo['name'].'</a>';

	$toptypeinfo = $typeinfo;

}
elseif($tid2)
{
	$dsql->Execute('me',"select * from `#@__asktype` where id='$tid2' ");
	if(!$typeinfo = $dsql->getarray())
	{
		showmsgs('browser_notexists','index.php');
		exit;
	}
	$wheresql .= "tid2='$tid2'";
	$multistr .="tid2=$tid2";
	$tidstr = "tid2=$tid2";

	$toptypeinfo = $dsql->getone("select id, name, asknum from `#@__asktype` where id='".$typeinfo['reid']."' limit 1");
	$navtitle = $typeinfo['name'].' '.$toptypeinfo['name'];
	$nav = ' '.$symbols.' <a href="browser.php?tid='.$toptypeinfo['id'].'">'.$toptypeinfo['name'].'</a> '.$symbols.' <a href="browser.php?tid2='.$tid2.'">'.$typeinfo['name'].'</a>';

}

if($tid || $tid2)
{
	$query = "select id, name, asknum from #@__asktype where reid='".$toptypeinfo['id']."' order by disorder asc, id asc";
	$subtypeinfos = array();
	$dsql->Execute('me',$query);
	while($row = $dsql->getarray())
	{
		$subtypeinfos[] = $row;
	}
}

$orderby = 'order by';
$all = array();
$all[0] = '';
$all[2] = '';
$all[3] = '';
$all[4] = '';
$all[5] = '';
$all[6] = '';

if(empty($lm))
{
	$wheresql .= ' and status>=0';
	$orderby .= ' disorder desc, dateline desc';
	$all[0] = ' class="thisclass"';
}elseif($lm == 1)
{
	//精彩问题
	$wheresql .= ' and digest=1';
	$orderby .= ' replies desc, dateline desc';
	$multistr .="&amp;lm=$lm";
	$nav .= ' '.$symbols.' 精彩推荐';
}elseif($lm == 2)
{
	//待解决
	$wheresql .= ' and status=0';
	$orderby .= ' disorder desc, dateline desc';
	$multistr .="&amp;lm=$lm";
	$nav .= ' '.$symbols.' 待解决问题';
	$all[2] = ' class="thisclass"';
}elseif($lm == 3)
{
	//已解决
	$wheresql .= ' and status=1';
	$orderby .= ' solvetime desc';
	$multistr .="&amp;lm=$lm";
	$nav .= ' '.$symbols.' 新解决问题';
	$all[3] = ' class="thisclass"';
}elseif($lm == 4)
{
	//高分
	$wheresql .= ' and status=0';
	$orderby .= ' reward desc';
	$multistr .="&amp;lm=$lm";
	$nav .= ' '.$symbols.' 高分问题';
	$all[4] = ' class="thisclass"';
}elseif($lm == 5)
{
	//零回答
	$wheresql .= ' and replies=0 and status=0';
	$orderby .= ' disorder desc, dateline desc';
	$multistr .="&amp;lm=$lm";
	$nav .= ' '.$symbols.' 零回答问题';
	$all[5] = ' class="thisclass"';
}elseif($lm == 6)
{
	//快到期
	$wheresql .= ' and status=0';
	$orderby .= ' expiredtime asc, dateline desc';
	$multistr .="&amp;lm=$lm";
	$nav .= ' '.$symbols.' 快到期问题';
	$all[6] = ' class="thisclass"';
}else
{
	showmsgs('browser_notexists','index.php');
	exit;
}

$navtitle = $navtitle == '' ? $sitename : $navtitle.' '.$sitename;
$nav = "<a href=\"$indexname\">$sitename</a>".$nav;

$wheresql = trim($wheresql);
if(eregi("^and", $wheresql))
{
	$wheresql = substr($wheresql,3);
}
$wheresql = 'where '.trim($wheresql);

if(eregi("^&amp;", $multistr))
{
	$multistr = substr($multistr,5);
}

$row = $dsql->getone("select count(*) as dd from `#@__ask` $wheresql");
$askcount = $row['dd'];
$realpages = @ceil($askcount/$tpp);
if($page > $realpages)
{
	$page = $realpages;
}
$page = isset($page) ? max(1, intval($page)) : 1;
$start_limit = ($page - 1) * $tpp;

$multipage = multi($askcount, $tpp, $page, "browser.php?$multistr");

$query = "select id, tid, tidname, tid2, tid2name, title, reward, dateline, status, expiredtime solvetime, replies
from `#@__ask` $wheresql $orderby limit $start_limit, $tpp";

$dsql->Execute('me',$query);
$asks = array();
while($row = $dsql->getarray())
{
	if($row['status'] == 1)
	{
		//已解决
		$row['status'] = 'qa_ico_2.gif';
	}elseif($row['status'] == 2)
	{
		//关闭
		$row['status'] = 'qa_ico_2.gif';
	}elseif($row['status'] == 3)
	{
		//过期
		$row['status'] = 'qa_ico_2.gif';
	}else
	{
		//正常
		$row['status'] = 'qa_ico_1.gif';
	}
	$row['dateline'] = gmdate('m-d', $row['dateline'] + ($timeoffset * 3600));
	$row['title'] = cn_substr($row['title'],40);
	$asks[] = $row;
}

//快到期的问题
$query = "select id, tid, tidname, tid2, tid2name, title from `#@__ask` where status=0 order by expiredtime asc, dateline desc limit 10";
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
$dtp->LoadTemplate(DEDEASK.'template/default/browser.htm');
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