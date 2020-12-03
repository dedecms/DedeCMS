<?php
if(!defined('DEDEINC'))
{
	exit("Request Error!");
}

function ch_softlinks($fvalue,&$ctag,&$refObj,$fname='',$downloadpage=false)
{
	global $dsql;
	$row = $dsql->GetOne("Select * From `#@__softconfig` ");
	$phppath = $GLOBALS['cfg_phpurl'];
	$downlinks = '';
	if($row['downtype']!='0' && !$downloadpage)
	{
		$tempStr = GetSysTemplets("channel_downlinkpage.htm");
		$links = $phppath."/download.php?open=0&aid=".$refObj->ArcID."&cid=".$refObj->ChannelID;
		$downlinks = str_replace("~link~",$links,$tempStr);
		return $downlinks;
	}
	else
	{
		return ch_softlinks_all($fvalue,$ctag,$refObj,$row);
	}
}


function ch_softlinks_all($fvalue,&$ctag,&$refObj,&$row)
{
	global $dsql;

	//引入权限判断
	require_once(DEDEINC."/memberlogin.class.php");
	$cfg_ml = new MemberLogin(-1);
	$query = "select daccess from ".$refObj->ChannelInfos['addtable']." where aid='".$refObj->ArcID."'";
	$daccess = $dsql->GetOne($query);
	if($cfg_ml->M_Rank < $daccess['daccess'])
	{
		return '你的权限不足或者未登录, 不能下载! 请登陆或者升级等级';
	}

	$phppath = $GLOBALS['cfg_phpurl'];
	$downlinks = '';
	$dtp = new DedeTagParse();
	$dtp->LoadSource($fvalue);
	if(!is_array($dtp->CTags))
	{
		$dtp->Clear();
		return "无链接信息！";
	}
	$tempStr = GetSysTemplets('channel_downlinks.htm');
	foreach($dtp->CTags as $ctag)
	{
		if($ctag->GetName()=='link')
		{
			$links = trim($ctag->GetInnerText());
			$serverName = trim($ctag->GetAtt('text'));
			$islocal = trim($ctag->GetAtt('islocal'));
			if(!isset($firstLink) && $islocal==1)
			{
				$firstLink = $links;
			}
			if($islocal==1 && $row['islocal']!=1)
			{
				continue;
			}
			else
			{
				//支持http,迅雷下载,ftp,flashget
				if(!eregi('^http://|^thunder://|^ftp://|^flashget://',$links))
				{
					$links = $GLOBALS['cfg_mainsite'].$links;
				}
				$downloads = getDownloads($links);
				if($row['gotojump']==1)
				{
					$links = $phppath."/download.php?open=1&id=".$refObj->ArcID."&link=".urlencode(base64_encode($links));
				}
				$temp = str_replace("~link~",$links,$tempStr);
				$temp = str_replace("~server~",$serverName,$temp);
				$temp = str_replace("~downloads~",$downloads,$temp);
				$downlinks .= $temp;
			}
		}
	}
	$dtp->Clear();

	//启用镜像功能的情况
	if($row['ismoresite']==1 && !empty($row['sites']) && isset($firstLink))
	{
		$firstLink = @eregi_replace($GLOBALS['cfg_basehost'],'',$firstLink);
		$row['sites'] = ereg_replace("[\r\n]{1,}","\n",$row['sites']);
		$sites = explode("\n",trim($row['sites']));
		foreach($sites as $site)
		{
			if(trim($site)=='')
			{
				continue;
			}
			list($link,$serverName) = explode('|',$site);
			$link = trim($link).$firstLink;
			$downloads = getDownloads($link);
			if($row['gotojump']==1)
			{
				$link = $phppath."/download.php?open=1&link=".urlencode(base64_encode($link));
			}
			$temp = str_replace("~link~",$link,$tempStr);
			$temp = str_replace("~server~",$serverName,$temp);
			$temp = str_replace("~downloads~",$downloads,$temp);
			$downlinks .= $temp;
		}
	}
	return $downlinks;
}

function getDownloads($url)
{
	global $dsql;
	$hash = md5($url);
	$query = "select downloads from #@__downloads where hash='$hash'";
	$row = $dsql->GetOne($query);
	if(is_array($row))
	{
		$downloads = $row['downloads'];
	}else
	{
		$downloads = 0;
	}
	return $downloads;
}

?>