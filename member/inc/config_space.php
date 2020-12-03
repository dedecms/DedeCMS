<?php
if(!defined('DEDEMEMBER'))
{
	exit('dedecms');
}

//检查是否开放会员功能
if($cfg_mb_open=='N')
{
	ShowMsg("系统关闭了会员功能，因此你无法访问此页面！","javascript:;");
	exit();
}

$_vars = GetUserSpaceInfos();
if($cfg_mb_spacesta > 10)
{
	if($_vars['rank'] < $cfg_mb_spacesta)
	{
		ShowMsg("该用户尚未开通空间！",'javascript:;');
		exit();
	}
}
if($_vars['spacestyle']=='')
{
	if($_vars['mtype']=='个人')
	{
		$_vars['spacestyle'] = 'person';
	}
	else if($_vars['mtype']=='企业')
	{
		$_vars['spacestyle'] = 'company';
	}
}

if($_vars['mtype'] == '企业' && isset($action))
{
	if($action == 'infos') $action = 'introduce';
}

//获取分类数据
$query = "select * from #@__mtypes where mid='".$_vars['mid']."'";
$dsql->SetQuery($query);
$dsql->Execute();
$mtypearr = array();
while($row = $dsql->GetArray())
{
	$mtypearr[] = $row;
}

//获取私有数据
if($_vars['mtype']=='企业')
{

	require_once(DEDEINC.'/enums.func.php');
	$query = "select * from `#@__member_company` where mid='".$_vars['mid']."'";
	$company = $db->GetOne($query);
	$company['vocation'] = GetEnumsValue('vocation', $company['vocation']);
	$company['cosize'] = GetEnumsValue('cosize', $company['cosize']);
	$tmpplace = GetEnumsTypes($company['place']);
	$provinceid = $tmpplace['top'];
	$provincename = (isset($em_nativeplaces[$provinceid]) ?  $em_nativeplaces[$provinceid] : '');
	$cityname = (isset($em_nativeplaces[$tmpplace['son']]) ? $em_nativeplaces[$tmpplace['son']] : '');
	$company['place'] = $provincename.' - '.$cityname;
	$_vars = array_merge($company, $_vars);
}

//找不到指定样式文件夹的时候使用person为默认
if(!is_dir(DEDEMEMBER.'/space/'.$_vars['spacestyle']))
{
	$_vars['spacestyle'] = 'person';
}


/**
 * 获取空间基本信息
 *
 * @return unknown
 */
function GetUserSpaceInfos()
{
	global $dsql,$uid,$cfg_memberurl;
	$_vars = array();
	$userid = ereg_replace("[\r\n\t \*%]",'',$uid);
	$query = "Select m.mid,m.mtype,m.userid,m.uname,m.sex,m.rank,m.email,m.scores,m.spacesta,m.face,m.logintime,s.*,t.*,r.membername
		          From `#@__member` m
		          left join `#@__member_space` s on s.mid=m.mid
		          left join `#@__member_tj` t on t.mid=m.mid
		          left join `#@__arcrank` r on r.rank=m.rank
		          where m.userid like '$uid' ";
	$_vars = $dsql->GetOne($query);
	if(!is_array($_vars))
	{
		ShowMsg("你访问的用户可能已经被删除！","javascript:;");
		exit();
	}
	if($_vars['face']=='')
	{
		if($_vars['sex']=='女')
		{
			$_vars['face'] = 'space/person/img/nopic.gif';
		}
		else
		{
			$_vars['face'] = 'space/person/img/nopic.gif';
		}
	}
	$_vars['userid_e'] = urlencode($_vars['userid']);
	$_vars['userurl'] = $cfg_memberurl."/index.php?uid=".$_vars['userid'];
	if($_vars['membername']=='开放浏览')
	{
		$_vars['membername'] = '限制会员';
	}
	return $_vars;
}

?>