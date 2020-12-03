<?php
if(!defined('DEDEINC')) exit('Request Error!');

function ch_softlinks($fvalue, &$ctag, &$refObj, $fname='', $downloadpage=false)
{
	global $dsql;
	$row = $dsql->GetOne("Select * From `#@__softconfig` ");
	$phppath = $GLOBALS['cfg_phpurl'];
	$downlinks = '';
	if($row['downtype']!='0' && !$downloadpage)
	{
		$tempStr = GetSysTemplets("channel_downlinkpage.htm");
		$links = $phppath."/download.php?open=0&aid=".$refObj->ArcID."&cid=".$refObj->ChannelID;
		$downlinks = str_replace("~link~", $links, $tempStr);
		return $downlinks;
	}
	else
	{
		return ch_softlinks_all($fvalue, $ctag, $refObj, $row);
	}
}

//读取所有链接地址
function ch_softlinks_all($fvalue, &$ctag, &$refObj, &$row)
{
	global $dsql, $cfg_phpurl;
	$phppath = $cfg_phpurl;
	$dtp = new DedeTagParse();
	$dtp->LoadSource($fvalue);
	if( !is_array($dtp->CTags) )
	{
		$dtp->Clear();
		return "无链接信息！";
	}
	$tempStr = GetSysTemplets('channel_downlinks.htm');
	$downlinks = '';
	foreach($dtp->CTags as $ctag)
	{
		if($ctag->GetName()=='link')
		{
			$link = trim($ctag->GetInnerText());
			$serverName = trim($ctag->GetAtt('text'));
			$islocal = trim($ctag->GetAtt('islocal'));
			
			//分析本地链接
			if(!isset($firstLink) && $islocal==1) $firstLink = $link;
			if($islocal==1 && $row['islocal'] != 1) continue;
	
			//支持http,迅雷下载,ftp,flashget
			if(!eregi('^http://|^thunder://|^ftp://|^flashget://', $link))
			{
					$link = $GLOBALS['cfg_mainsite'].$link;
			}
			$downloads = getDownloads($link);
			$uhash = substr(md5($link), 0, 24);
			if($row['gotojump']==1)
			{
				$link = $phppath."/download.php?open=2&id={$refObj->ArcID}&uhash={$uhash}";
			}
			$temp = str_replace("~link~",$link,$tempStr);
			$temp = str_replace("~server~",$serverName,$temp);
			$temp = str_replace("~downloads~",$downloads,$temp);
			$downlinks .= $temp;
		}
	}
	$dtp->Clear();
	//获取镜像功能的地址
	//必须设置为：[根据本地地址和服务器列表自动生成] 的情况
	$linkCount = 1;
	if($row['ismoresite']==1 && $row['moresitedo']==1 && trim($row['sites'])!='' && isset($firstLink))
	{
		$firstLink = eregi_replace("http://([^/]*)/", '/', $firstLink);
		$row['sites'] = ereg_replace("[\r\n]{1,}", "\n", $row['sites']);
		$sites = explode("\n", trim($row['sites']));
		foreach($sites as $site)
		{
			if(trim($site)=='') continue;
			list($link,$serverName) = explode('|', $site);
			$link = trim( ereg_replace("/$", "",$link) ).$firstLink;
			$downloads = getDownloads($link);
			$uhash = substr(md5($link), 0, 24);
			if($row['gotojump']==1)
			{
				$link = $phppath."/download.php?open=2&id={$refObj->ArcID}&uhash={$uhash}";
			}
			$temp = str_replace("~link~", $link, $tempStr);
			$temp = str_replace("~server~", $serverName, $temp);
			$temp = str_replace("~downloads~", $downloads, $temp);
			$downlinks .= $temp;
		}
	}
	return $downlinks;
}

function getDownloads($url)
{
	global $dsql;
	$hash = md5($url);
	$query = "select downloads from `#@__downloads` where hash='$hash' ";
	$row = $dsql->GetOne($query);
	if(is_array($row))
	{
		$downloads = $row['downloads'];
	}
	else
	{
		$downloads = 0;
	}
	return $downloads;
}

?>