<?php
function DelArc($aid,$type='ON',$onlyfile=false)
{
	global $dsql,$cfg_cookie_encode,$cfg_multi_site,$cfg_medias_dir;
	global $cuserLogin,$cfg_upload_switch,$cfg_delete,$cfg_basedir;
	global $admin_catalogs, $cfg_admin_channel;
	
	if($cfg_delete == 'N') $type = 'OK';
	if(empty($aid)) return ;
	$aid = ereg_replace("[^0-9]", '', $aid);
	$arctitle = $arcurl = '';

	//查询表信息
	$query = "Select ch.maintable,ch.addtable,ch.nid,ch.issystem From `#@__arctiny` arc
	            left join `#@__arctype` tp on tp.id=arc.typeid
              left join `#@__channeltype` ch on ch.id=arc.channel where arc.id='$aid' ";
	$row = $dsql->GetOne($query);
	$nid = $row['nid'];
	$maintable = (trim($row['maintable'])=='' ? '#@__archives' : trim($row['maintable']));
	$addtable = trim($row['addtable']);
	$issystem = $row['issystem'];

	//查询档案信息
	if($issystem==-1)
	{
		$arcQuery = "Select arc.*,tp.* from `$addtable` arc left join `#@__arctype` tp on arc.typeid=tp.id where arc.aid='$aid' ";
	}
	else
	{
		$arcQuery = "Select arc.*,tp.*,arc.id as aid from `$maintable` arc left join `#@__arctype` tp on arc.typeid=tp.id where arc.id='$aid' ";
	}

	$arcRow = $dsql->GetOne($arcQuery);

	//检测权限
	if(!TestPurview('a_Del,sys_ArcBatch'))
	{
		if(TestPurview('a_AccDel'))
		{
			if( !in_array($arcRow['typeid'], $admin_catalogs) && (count($admin_catalogs) != 0 || $cfg_admin_channel != 'all') )
			{
				return false;
			}
		}
		else if(TestPurview('a_MyDel'))
		{
			if($arcRow['mid'] != $cuserLogin->getUserID())
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}

	//$issystem==-1 是单表模型，不使用回收站
	if($issystem == -1) $type = 'OK';
	if(!is_array($arcRow)) return false;
	
	/** 删除到回收站 **/
	if($cfg_delete == 'Y' && $type == 'ON')
	{
		$dsql->ExecuteNoneQuery("Update `$maintable` set arcrank='-2' where id='$aid' ");
		$dsql->ExecuteNoneQuery("Update `#@__arctiny` set `arcrank` = '-2' where id = '$aid'; ");
	}
	else
	{
		//删除数据库记录
		if(!$onlyfile)
		{
			//删除相关附件
			if($cfg_upload_switch == 'Y')
			{
				$dsql->Execute("me", "SELECT * FROM `#@__uploads` WHERE arcid = '$aid'");
				while($row = $dsql->GetArray('me'))
				{
					$addfile = $row['url'];
					$aid = $row['aid'];
					$dsql->ExecuteNoneQuery("Delete From `#@__uploads` where aid = '$aid' ");
					$upfile = $cfg_basedir.$addfile;
					if(@file_exists($upfile)) @unlink($upfile);
				}
			}
			$dsql->ExecuteNoneQuery("Delete From `#@__arctiny` where id='$aid'");
			if($addtable != '')
			{
				$dsql->ExecuteNoneQuery("Delete From `$addtable` where aid='$aid' ");
			}
			if($issystem != -1)
			{
				$dsql->ExecuteNoneQuery("Delete From `#@__archives` where id='$aid' ");
			}
			$dsql->ExecuteNoneQuery("Delete From `#@__feedback` where aid='$aid' ");
			$dsql->ExecuteNoneQuery("Delete From `#@__member_stow` where aid='$aid' ");
			$dsql->ExecuteNoneQuery("Delete From `#@__taglist` where aid='$aid' ");
			$dsql->ExecuteNoneQuery("Delete From `#@__erradd` where aid='$aid' ");
		}

		//删除文本数据
		$filenameh = DEDEDATA."/textdata/".(ceil($aid/5000))."/{$aid}-".substr(md5($cfg_cookie_encode),0,16).".txt";
		if(@is_file($filenameh)) @unlink($filenameh);
		
	}
	
	if(empty($arcRow['money'])) $arcRow['money'] = 0;
	if(empty($arcRow['ismake'])) $arcRow['ismake'] = 1;
	if(empty($arcRow['arcrank'])) $arcRow['arcrank'] = 0;
	if(empty($arcRow['filename'])) $arcRow['filename'] = '';

	//删除HTML
	if($arcRow['ismake']==-1 || $arcRow['arcrank']!=0 || $arcRow['typeid']==0 || $arcRow['money']>0)
	{
		return true;
	}

	//强制转换非多站点模式，以便统一方式获得实际HTML文件
	$GLOBALS['cfg_multi_site'] = 'N';
	$arcurl = GetFileUrl($arcRow['aid'],$arcRow['typeid'],$arcRow['senddate'],$arcRow['title'],$arcRow['ismake'],
                       $arcRow['arcrank'],$arcRow['namerule'],$arcRow['typedir'],$arcRow['money'],$arcRow['filename']);
	if(!ereg("\?", $arcurl))
	{
		$htmlfile = GetTruePath().str_replace($GLOBALS['cfg_basehost'],'',$arcurl);
		if(file_exists($htmlfile) && !is_dir($htmlfile))
		{
			@unlink($htmlfile);
			$arcurls = explode(".", $htmlfile);
			$sname = $arcurls[count($arcurls)-1];
			$fname = ereg_replace("(\.$sname)$", "", $htmlfile);
			for($i=2; $i<=100; $i++)
			{
				$htmlfile = $fname."_{$i}.".$sname;
				if( @file_exists($htmlfile) ) @unlink($htmlfile);
				else break;
			}
		}
	}

	return true;
}

//获取真实路径
function GetTruePath($siterefer='', $sitepath='')
{
	$truepath = $GLOBALS['cfg_basedir'];
	return $truepath;
}
?>