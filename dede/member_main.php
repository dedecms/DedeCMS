<?php
require_once(dirname(__FILE__)."/config.php");
CheckPurview('member_List');
require_once(DEDEINC."/datalistcp.class.php");
setcookie("ENV_GOBACK_URL",$dedeNowurl,time()+3600,"/");
if(!isset($sex))
{
	$sex = '';
}
if(!isset($mtype))
{
	$mtype = '';
}
if(!isset($sex))
{
	$sex = '';
}
if(!isset($keyword))
{
	$keyword = '';
}
else
{
	$keyword = trim(FilterSearch($keyword));
}
if(!isset($att))
{
	$att = '';
}
if($mtype=='')
{
	$mtypeform = "<option value=''>会员类型</option>\r\n";
}
else
{
	$mtypeform = "<option value='$mtype'>$mtype</option>\r\n";
}
if($sex=='')
{
	$sexform = "<option value=''>性别</option>\r\n";
}
else
{
	$sexform = "<option value='$sex'>$sex</option>\r\n";
}
if(empty($sortkey))
{
	$sortkey = 'mid';
}
else
{
	$sortkey = eregi_replace('[^a-z]','',$sortkey);
}
if($sortkey=="mid")
{
	$sortform = "<option value='mid'>mid/注册时间</option>\r\n";
}
else if($sortkey=="rank")
{
	$sortform = "<option value='rank'>会员等级</option>\r\n";
}
else if($sortkey=="money")
{
	$sortform = "<option value='money'>会员金币</option>\r\n";
}
else if($sortkey=="scores")
{
	$sortform = "<option value='scores'>会员积分</option>\r\n";
}
else
{
	$sortform = "<option value='logintime'>登录时间</option>\r\n";
}
$wheres[] = " (userid like '%$keyword%' Or uname like '%$keyword%' Or email like '%$keyword%') ";
if($sex   != '')
{
	$wheres[] = " sex like '$sex' ";
}
if($mtype != '')
{
	$wheres[] = " mtype like '$mtype' ";
}
$attform = '';
if(!empty($att))
{
	if($att=='ad')
	{
		$attform = "<option value='ad'>被推荐会员</option>\r\n";
		$wheres[] = " matt=1 ";
	}else if($att=='uprank')
	{
		$attform = "<option value='uprank'>待升级会员</option>\r\n";
		$wheres[] = " uprank>0 ";
	}else if($att=='upmoney')
	{
		$attform = "<option value='upmoney'>待充充值会员</option>\r\n";
		$wheres[] = " upmoney>0 ";
	}else if($att=='mb')
	{
		$attform = "<option value='mb'>非普通会员</option>\r\n";
		$wheres[] .= " rank>10 ";
	}
}
$MemberTypes = array();
$dsql->SetQuery("Select rank,membername From `#@__arcrank` where rank>0 ");
$dsql->Execute();
while($row = $dsql->GetObject())
{
	$MemberTypes[$row->rank] = $row->membername;
}
$whereSql = join(' And ',$wheres);
if($whereSql!='')
{
	$whereSql = ' where '.$whereSql;
}
$sql  = "select * From `#@__member` $whereSql order by $sortkey desc ";
$dlist = new DataListCP();
$dlist->SetParameter('sex',$sex);
$dlist->SetParameter('att',$att);
$dlist->SetParameter('mtype',$mtype);
$dlist->SetParameter('sortkey',$sortkey);
$dlist->SetParameter('keyword',$keyword);
$dlist->SetTemplet(DEDEADMIN."/templets/member_main.htm");
$dlist->SetSource($sql);
$dlist->display();

function GetMemberName($rank,$mt)
{
	global $MemberTypes;
	if(isset($MemberTypes[$rank]))
	{
		if($mt=='ut')
		{
			return " <font color='red'>待升级：".$MemberTypes[$rank]."</font>";
		}
		else
		{
			return $MemberTypes[$rank];
		}
	}
	else
	{
		if($mt=='ut')
		{
			return '';
		}
		else
		{
			return $mt;
		}
	}
}

function GetUpMoney($m)
{
	if($m>0)
	{
		return " <font color='red'>申请：$m 金币</font> ";
	}
	else
	{
		return '';
	}
}

function GetMAtt($m){
	if($m<1)
	{
		return '[否]';
	}
	else if($m==10)
	{
		return "<font color='red'>[管理员]</font>";
	}
	else
	{
		return "<img src='img/adminuserico.gif' wmidth='16' height='15'><font color='red'>[荐]</font>";
	}
}

?>