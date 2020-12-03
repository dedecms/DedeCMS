<?php
if(!defined('DEDEMEMBER'))
{
	exit('dedecms');
}

/*---------------------------------
文章列表
function list_article(){ }
-------------------------------------*/
if($action=='article')
{
	if(empty($mtype)) {
		$mtype = 0;
	}
	include_once(DEDEINC.'/arc.memberlistview.class.php');
	include_once(DEDEINC."/channelunit.func.php");
	$query = "Select arc.*,tp.typedir,tp.typename,tp.isdefault,tp.defaultname,tp.namerule,tp.namerule2,tp.ispart,tp.moresite,tp.siteurl,tp.sitepath
		  from `#@__archives` arc left join `#@__arctype` tp on arc.typeid=tp.id where arc.mid='{$_vars['mid']}' And arc.channel=1 order by arc.id desc";
	$dlist = new MemberListview();
	$dlist->pageSize = 8;
	$dlist->SetParameter("mtype",$mtype);
	$dlist->SetParameter("uid",$_vars['userid']);
	$dlist->SetParameter("action",$action);
	$dlist->SetTemplate(DEDEMEMBER."/space/{$_vars['spacestyle']}/listarticle.htm");
	$dlist->SetSource($query);
	$dlist->Display();
	exit();
}

/*---------------------------------
所有文档列表
function list_archives(){ }
-------------------------------------*/
else if($action=='archives')
{
	if(empty($mtype)) {
		$mtype = 0;
	}
	include_once(DEDEINC.'/arc.memberlistview.class.php');
	include_once(DEDEINC."/channelunit.func.php");
	
	//如果没指定频道ID的情况下，列出所有非单表模型文档
	if(empty($channelid))
	{
		$channelid = 0;
		$query = "Select arc.*,tp.typedir,tp.typename,tp.isdefault,tp.defaultname,tp.namerule,tp.namerule2,tp.ispart,tp.moresite,tp.siteurl,tp.sitepath            from `#@__archives` arc left join `#@__arctype` tp on arc.typeid=tp.id
		         where arc.mid='{$_vars['mid']}' order by arc.id desc";
	}
	else
	{
		$channelid = intval($channelid);
		$chRow = $dsql->GetOne("Select issystem,addtable,listfields From `#@__channeltype` where id='$channelid' ");
		if(!is_array($chRow)) {
			die(" Channel Error! ");
		}
		if($chRow['issystem']==-1)
		{
			$addtable = trim($chRow['addtable']);
			$listfields = explode(',',$chRow['listfields']);
			$listfields_str = 'arc.'.join(',arc.',$listfields);
			if($listfields_str!='arc.') {
				$listfields_str = $listfields_str.',';
			}
			else {
				$listfields_str = '';
			}
			
			$query = "Select arc.aid,arc.aid as id,arc.typeid,1 as ismake,0 as money,'' as filename,{$listfields_str}
			       tp.typedir,tp.typename,tp.isdefault,tp.defaultname,tp.namerule,tp.namerule2,tp.ispart,tp.moresite,tp.siteurl,tp.sitepath
			       from `{$addtable}` arc left join `#@__arctype` tp on arc.typeid=tp.id
		         where arc.mid='{$_vars['mid']}' And arc.channel='$channelid' order by arc.aid desc";
		}
		else
		{
			$query = "Select arc.*,tp.typedir,tp.typename,tp.isdefault,tp.defaultname,tp.namerule,tp.namerule2,tp.ispart,tp.moresite,tp.siteurl,tp.sitepath            from `#@__archives` arc left join `#@__arctype` tp on arc.typeid=tp.id
		         where arc.mid='{$_vars['mid']}' And arc.channel='$channelid' order by arc.id desc";
		}
	}
	$dlist = new MemberListview();
	$dlist->pageSize = 8;
	$dlist->SetParameter("mtype",$mtype);
	$dlist->SetParameter("uid",$_vars['userid']);
	$dlist->SetParameter("channelid",$channelid);
	$dlist->SetParameter("action",$action);
	$dlist->SetTemplate(DEDEMEMBER."/space/{$_vars['spacestyle']}/listarchives.htm");
	$dlist->SetSource($query);
	$dlist->Display();
	exit();
}

/*---------------------------------
所有文档列表
function list_album(){ }
-------------------------------------*/
else if($action=='album')
{
	if(empty($mtype)) {
		$mtype = 0;
	}
	include_once(DEDEINC.'/arc.memberlistview.class.php');
	include_once(DEDEINC."/channelunit.func.php");
	$query = "Select arc.*,tp.typedir,tp.typename,tp.isdefault,tp.defaultname,tp.namerule,tp.namerule2,tp.ispart,tp.moresite,tp.siteurl,tp.sitepath
		  from `#@__archives` arc left join `#@__arctype` tp on arc.typeid=tp.id where arc.mid='{$_vars['mid']}' And arc.channel=2 order by arc.id desc";
	$dlist = new MemberListview();
	$dlist->pageSize = 8;
	$dlist->SetParameter("mtype",$mtype);
	$dlist->SetParameter("uid",$_vars['userid']);
	$dlist->SetParameter("action",$action);
	$dlist->SetTemplate(DEDEMEMBER."/space/{$_vars['spacestyle']}/listalbum.htm");
	$dlist->SetSource($query);
	$dlist->Display();
	exit();
}

/*---------------------------------
留言本
function guestbook(){ }
-------------------------------------*/
else if($action=='guestbook')
{
	if(empty($mtype)) {
		$mtype = 0;
	}
	include_once(DEDEINC.'/datalistcp.class.php');
	$query = "Select * From `#@__member_guestbook` where mid='{$_vars['mid']}' order by aid desc";
	$dlist = new DataListCP();
	$dlist->pageSize = 8;
	$dlist->SetParameter("uid",$_vars['userid']);
	$dlist->SetParameter("action",$action);
	$dlist->SetTemplate(DEDEMEMBER."/space/{$_vars['spacestyle']}/guestbook.htm");
	$dlist->SetSource($query);
	$dlist->Display();
	exit();
}

/*---------------------------------
我的好友
function friend(){ }
-------------------------------------*/
else if($action=='friend')
{
	if(empty($mtype)) {
		$mtype = 0;
	}
	include_once(DEDEINC.'/arc.memberlistview.class.php');
	include_once(DEDEINC."/channelunit.func.php");
	$query = "Select arc.*,tp.typedir,tp.typename,tp.isdefault,tp.defaultname,tp.namerule,tp.namerule2,tp.ispart,tp.moresite,tp.siteurl,tp.sitepath
		  from `#@__archives` arc
		  left join `#@__arctype` tp on arc.typeid=tp.id
		  where arc.mid='{$_vars['mid']}' order by arc.id desc";
	$dlist = new MemberListview();
	$dlist->pageSize = 8;
	$dlist->SetParameter("mtype",$mtype);
	$dlist->SetParameter("uid",$_vars['userid']);
	$dlist->SetParameter("action",$action);
	$dlist->SetTemplate(DEDEMEMBER."/space/{$_vars['spacestyle']}/friend.htm");
	$dlist->SetSource($query);
	$dlist->Display();
	exit();
}

/*---------------------------------
我的好友
function infos(){ }
-------------------------------------*/
else if($action=='infos')
{
	include_once(DEDEDATA.'/enums/nativeplace.php');
	include_once(DEDEINC."/enums.func.php");
	$row = $dsql->GetOne("select  * from `#@__member_person` where mid='{$_vars['mid']}' ");
	$dpl = new DedeTemplate();
	$dpl->LoadTemplate(DEDEMEMBER."/space/{$_vars['spacestyle']}/infos.htm");
	$dpl->display();
}

/*---------------------------------
保存留言
function guestbook_save(){ }
-------------------------------------*/
else if($action=='guestbooksave')
{
	CheckRank(0,0);
	$svali = GetCkVdValue();
	if(strtolower($vdcode)!=$svali || $svali=='')
	{
		ResetVdValue();
		ShowMsg("验证码错误！","-1");
		exit();
	}
	$uidnum = intval($uidnum);
	if(empty($uidnum))
	{
		ShowMsg("参数错误！","-1");
		exit();
	}
	if(strlen($title)<2||strlen($msg)<10)
	{
		ShowMsg("你的标题不合法或留言内容太短！","-1");
		exit();
	}
	$uname = HtmlReplace($uname,1);
	$email = HtmlReplace($email,1);
	$qq = HtmlReplace($qq,1);
	$tel = HtmlReplace($tel,1);
	$title = cn_substrR(HtmlReplace($title,1),60);
	$msg = cn_substrR(HtmlReplace($msg),2048);
	if($cfg_ml->M_UserName!="" && $cfg_ml->M_ID!=$uidnum)
	{
		$gid = $cfg_ml->M_UserName;
	}
	else
	{
		$gid = '';
	}
	$inquery = "INSERT INTO `#@__member_guestbook`(mid,gid,title,msg,uname,email,qq,tel,ip,dtime)
   VALUES ('$uidnum','$gid','$title','$msg','$uname','$email','$qq','$tel','".GetIP()."',".time()."); ";
	$dsql->ExecuteNoneQuery($inquery);
	ShowMsg("成功提交你的留言！","-1");
	exit();
}

/*---------------------------------
加好友
function newfriend(){ }
-------------------------------------*/
else if($action=='newfriend')
{
	CheckRank(0,0);
	if($_vars['mid']==$cfg_ml->M_ID)
	{
		ShowMsg("你不能加自己为好友！","index.php?uid=".$uid);
		exit();
	}
	$addtime = time();
	$row = $dsql->GetOne("Select * From `#@__member_friends` where fid='{$_vars['mid']}' And mid='{$cfg_ml->M_ID}' ");
	if(is_array($row))
	{
		ShowMsg("该用户已经是你的好友！","index.php?uid=".$uid);
		exit();
	}
	else
	{
		$inquery = "INSERT INTO `#@__member_friends` (`fid` , `floginid` , `funame` , `mid` , `addtime` , `ftype`)
                VALUES ('{$_vars['mid']}' , '{$_vars['userid']}' , '{$_vars['uname']}' , '{$cfg_ml->M_ID}' , '$addtime' , '0'); ";
		$dsql->ExecuteNoneQuery($inquery);
		//统计我的好友数量
		$row = $dsql->GetOne("SELECT COUNT(*) AS nums FROM `#@__member_friends` WHERE `mid`='".$cfg_ml->M_ID."'");
		$dsql->ExecuteNoneQuery("UPDATE `#@__member_tj` SET friend='$row[nums]' WHERE `mid`='".$cfg_ml->M_ID."'");
		
		ShowMsg("成功添加好友！","index.php?uid=".$uid);
		exit();
	}
}

/*---------------------------------
加黑名单
function blackfriend(){ }
-------------------------------------*/
else if($action=='blackfriend')
{
	CheckRank(0,0);
	if($_vars['mid']==$cfg_ml->M_ID)
	{
		ShowMsg("你不能加自己到黑名单！","index.php?uid=".$uid);
		exit();
	}
	$addtime = time();
	$row = $dsql->GetOne("Select * From `#@__member_friends` where fid='{$_vars['mid']}' And mid='{$cfg_ml->M_ID}' ");
	if(is_array($row))
	{
		ShowMsg("该用户已经是你的好友！","index.php?uid=".$uid);
		exit();
	}
	else
	{
		$inquery = "INSERT INTO `#@__member_friends` (`fid` , `floginid` , `funame` , `mid` , `addtime` , `ftype`)
                VALUES ('{$cfg_ml->M_ID}' , '{$cfg_ml->M_LoginID}' , '{$cfg_ml->M_UserName}' , '{$_vars['mid']}' , '$addtime' , '-1'); ";
		$dsql->ExecuteNoneQuery($inquery);
		ShowMsg("成功添加好友在黑名单！","index.php?uid=".$uid);
		exit();
	}
}
/*--------------------
function _contact_introduce() {}
公司简介
---------------------*/
elseif($action == 'introduce')
{
	/*
	$query = "select *
	from #@__member_company
	where mid='".$_vars['mid']."'";
	$row = $dsql->GetOne($query);
	$_vars = array_merge($_vars, $row);
	*/
	$dpl = new DedeTemplate();
	$dpl->LoadTemplate(DEDEMEMBER."/space/{$_vars['spacestyle']}/introduce.htm");
	$dpl->display();
}

//联系我们
elseif ($action == 'contact')
{
	$dpl = new DedeTemplate();
	$dpl->LoadTemplate(DEDEMEMBER."/space/{$_vars['spacestyle']}/contact.htm");
	$dpl->display();
}
/*-------------------------------
function _products_news() { }
公司产品或新闻
--------------------------------*/
elseif($action == 'products' || $action == 'news')
{
	$mtype = isset($mtype) && is_numeric($mtype) ? $mtype : 0;
	if($action == 'products') {
		$channel = 6;
	}
	elseif ($action == 'news') {
		$channel =1 ;
	}
	include_once(DEDEINC.'/arc.memberlistview.class.php');
	include_once(DEDEINC."/channelunit.func.php");


	if($mtype == 0)
	{
		$query = "Select arc.*,tp.typedir,tp.typename,tp.isdefault,tp.defaultname,tp.namerule,
		tp.namerule2,tp.ispart,tp.moresite,tp.siteurl,tp.sitepath from `#@__archives` arc
		left join `#@__arctype` tp on arc.typeid=tp.id
		where arc.mid='{$_vars['mid']}' and arc.channel='$channel' order by arc.id desc";
	}
	else
	{
		$query = "Select arc.*,tp.typedir,tp.typename,tp.isdefault,tp.defaultname,tp.namerule,
		tp.namerule2,tp.ispart,tp.moresite,tp.siteurl,tp.sitepath from `#@__archives` arc
		left join `#@__arctype` tp on tp.id=arc.typeid
		left join #@__member_archives mt on mt.id=arc.id
		where arc.mid='{$_vars['mid']}' and arc.channel='$channel' and mt.mtypeid='$mtype' order by arc.id desc";
	}
	$dlist = new MemberListview();
	$dlist->pageSize = 8;
	$dlist->SetParameter("mtype",$mtype);
	$dlist->SetParameter("uid",$_vars['userid']);
	$dlist->SetParameter("action",$action);
	$dlist->SetTemplate(DEDEMEMBER."/space/{$_vars['spacestyle']}/listshop.htm");
	$dlist->SetSource($query);
	$dlist->Display();
	exit();
}
?>