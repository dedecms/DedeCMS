<?php
require_once(dirname(__FILE__)."/config.php");

$uid=empty($uid)? "" : RemoveXSS($uid); 

if(empty($action))
{
	$action = '';
}
if(empty($aid))
{
	$aid = '';
}
$menutype = 'mydede';

//会员后台
if($uid=='')
{
	$iscontrol = 'yes';
	if(!$cfg_ml->IsLogin())
	{
		include_once(dirname(__FILE__)."/templets/index-notlogin.htm");
	}
	else
	{
		$minfos = $dsql->GetOne("Select * From `#@__member_tj` where mid='".$cfg_ml->M_ID."'; ");
		$minfos['totaluse'] = $cfg_ml->GetUserSpace();
		$minfos['totaluse'] = number_format($minfos['totaluse']/1024/1024,2);
		if($cfg_mb_max > 0) {
			$ddsize = ceil( ($minfos['totaluse']/$cfg_mb_max) * 100 );
		}
		else {
			$ddsize = 0;
		}

		require_once(DEDEINC.'/channelunit.func.php');

		/* 最新文档8条 */
		$archives = array();
		$sql = "select arc.*, category.namerule, category.typedir, category.moresite, category.siteurl, category.sitepath, mem.userid
		from #@__archives arc
		left join #@__arctype category on category.id=arc.typeid
		left join #@__member mem on mem.mid=arc.mid
		where arc.arcrank > -1
		order by arc.sortrank desc limit 8";
		$dsql->SetQuery($sql);
		$dsql->Execute();
		while ($row = $dsql->GetArray())
		{
			$row['htmlurl'] = GetFileUrl($row['id'], $row['typeid'], $row['senddate'], $row['title'], $row['ismake'], $row['arcrank'], $row['namerule'], $row['typedir'], $row['money'], $row['filename'], $row['moresite'], $row['siteurl'], $row['sitepath']);
			$archives[] = $row;
		}

		/** 调用访客记录 **/
		$_vars['mid'] = $cfg_ml->M_ID;
		
		if(empty($cfg_ml->fields['face']))
		{
			$cfg_ml->fields['face']=($cfg_ml->fields['sex']=='女')? 'templets/images/dfgirl.png' : 'templets/images/dfboy.png';
		}

		/** 我的收藏 **/
		$favorites = array();
		$dsql->Execute('fl',"Select * From `#@__member_stow` where mid='{$cfg_ml->M_ID}'  limit 5");
		while($arr = $dsql->GetArray('fl'))
		{
			$favorites[] = $arr;
		}
		
        /** 欢迎新朋友 **/
		$sql = "SELECT * FROM `#@__member` ORDER BY mid desc limit 3";
		$newfriends = array();
		$dsql->SetQuery($sql);
		$dsql->Execute();
		while ($row = $dsql->GetArray()) {
			$newfriends[] = $row;
		}

		/** 好友记录 **/
		$sql = "SELECT F.*,M.face,M.sex FROM `#@__member` AS M LEFT JOIN #@__member_friends AS F ON F.fid=M.mid WHERE F.mid='{$cfg_ml->M_ID}' ORDER BY F.addtime desc LIMIT 6";
		$friends = array();
		$dsql->SetQuery($sql);
		$dsql->Execute();
		while ($row = $dsql->GetArray()) {
			$friends[] = $row;
		}
		
		/** 有没新短信 **/
		$pms = $dsql->GetOne("SELECT COUNT(*) AS nums FROM #@__member_pms WHERE toid='{$cfg_ml->M_ID}' AND `hasview`=0 AND folder = 'inbox'");	
		
		/** 查询会员状态 **/
		$moodmsg = $dsql->GetOne("SELECT * FROM #@__member_msg WHERE mid='{$cfg_ml->M_ID}' ORDER BY dtime desc");	

		/** 会员操作日志 **/
		$sql = "SELECT * From `#@__member_feed` where ischeck=1 order by fid desc limit 8";
		$feeds = array();
		$dsql->SetQuery($sql);
		$dsql->Execute();
		while ($row = $dsql->GetArray()) {
			$feeds[] = $row;
		}

		$dpl = new DedeTemplate();
		$tpl = dirname(__FILE__)."/templets/index.htm";
		$dpl->LoadTemplate($tpl);
		$dpl->display();
	}
}

/*-----------------------------
//会员空间主页
function space_index(){  }
------------------------------*/
else
{
	require_once(DEDEMEMBER.'/inc/config_space.php');
	if($action == '')
	{
		include_once(DEDEINC."/channelunit.func.php");
		$dpl = new DedeTemplate();
		$tplfile = DEDEMEMBER."/space/{$_vars['spacestyle']}/index.htm";

		//更新最近访客记录及站点统计记录
		$vtime = time();
		$last_vtime = GetCookie('last_vtime');
		$last_vid = GetCookie('last_vid');
		if(empty($last_vtime))
		{
			$last_vtime = 0;
		}
		if($vtime - $last_vtime > 3600 || !eregi(','.$uid.',',','.$last_vid.',') )
		{
			if($last_vid!='')
			{
				$last_vids = explode(',',$last_vid);
				$i = 0;
				$last_vid = $uid;
				foreach($last_vids as $lsid)
				{
					if($i>10)
					{
						break;
					}
					else if($lsid != $uid)
					{
						$i++;
						$last_vid .= ','.$last_vid;
					}
				}
			}
			else
			{
				$last_vid = $uid;
			}
			PutCookie('last_vtime', $vtime, 3600*24, '/');
			PutCookie('last_vid', $last_vid, 3600*24, '/');
			if($cfg_ml->IsLogin() && $cfg_ml->M_LoginID != $uid)
			{
				$vip = GetIP();
				$arr = $dsql->GetOne("Select * From `#@__member_vhistory` where mid='{$_vars['mid']}' And vid='{$cfg_ml->M_ID}' ");
				if(is_array($arr))
				{
					$dsql->ExecuteNoneQuery("Update `#@__member_vhistory` set vip='$vip',vtime='$vtime',count=count+1 where mid='{$_vars['mid']}' And vid='{$cfg_ml->M_ID}' ");
				}
				else
				{
					$query = "Insert Into `#@__member_vhistory`(mid,loginid,vid,vloginid,count,vip,vtime)
		 	  	          Values('{$_vars['mid']}','{$_vars['userid']}','{$cfg_ml->M_ID}','{$cfg_ml->M_LoginID}','1','$vip','$vtime'); ";
					$dsql->ExecuteNoneQuery($query);
				}
			}
			$dsql->ExecuteNoneQuery("Update `#@__member_tj` set homecount=homecount+1 where mid='{$_vars['mid']}' ");
		}
		$dpl->LoadTemplate($tplfile);
		$dpl->display();
		exit();
	}
	else
	{
		require_once(DEDEMEMBER.'/inc/space_action.php');
		exit();
	}
}

?>