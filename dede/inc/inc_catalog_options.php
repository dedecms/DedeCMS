<?php
function GetOptionList($selid=0, $userCatalog=0, $channeltype=0)
{
	global $OptionArrayList, $channels, $dsql, $cfg_admin_channel, $admin_catalogs;

	$dsql->SetQuery("Select id,typename From `#@__channeltype` ");
	$dsql->Execute();
	$channels = Array();
	while($row = $dsql->GetObject()) $channels[$row->id] = $row->typename;

	$OptionArrayList = '';

	//当前选中的栏目
	if($selid > 0)
	{
		$row = $dsql->GetOne("Select id,typename,ispart,channeltype From `#@__arctype` where id='$selid'");
		if($row['ispart']==1) $OptionArrayList .= "<option value='".$row['id']."' class='option1' selected='selected'>".$row['typename']."(封面频道)</option>\r\n";
		else $OptionArrayList .= "<option value='".$row['id']."' selected='selected'>".$row['typename']."</option>\r\n";
	}

	//是否限定用户管理的栏目
	if( $cfg_admin_channel=='array' )
	{ 
		if(count($admin_catalogs)==0)
		{
			$query = "Select id,typename,ispart,channeltype From `#@__arctype` where 1=2 ";
		}
		else
		{
			$admin_catalog = join(',', $admin_catalogs);
			$dsql->SetQuery("Select reid From `#@__arctype` where id in($admin_catalog) group by reid ");
			$dsql->Execute();
			$topidstr = '';
			while($row = $dsql->GetObject())
			{
				if($row->reid==0) continue;
				$topidstr .= ($topidstr=='' ? $row->reid : ','.$row->reid);
			}
			$admin_catalog .= ','.$topidstr;
			$admin_catalogs = explode(',', $admin_catalog);
			$admin_catalogs = array_unique($admin_catalogs);
			$admin_catalog = join(',', $admin_catalogs);
			 $admin_catalog = preg_replace("/,$/", '', $admin_catalog);
			$query = "Select id,typename,ispart,channeltype From `#@__arctype` where id in($admin_catalog) And reid=0 And ispart<>2 ";
		}
	}
	else
	{
		$query = "Select id,typename,ispart,channeltype From `#@__arctype` where ispart<>2 And reid=0 order by sortrank asc ";
	}

	$dsql->SetQuery($query);
	$dsql->Execute();

	while($row=$dsql->GetObject())
	{
		$sonCats = '';
		LogicGetOptionArray($row->id, '─', $channeltype, $dsql, $sonCats);
		if($sonCats != '')
		{
			if($row->ispart==1) $OptionArrayList .= "<option value='".$row->id."' class='option1'>".$row->typename."(封面频道)</option>\r\n";
			else if($row->ispart==2) $OptionArrayList .= '';
			else if( empty($channeltype) && $row->ispart != 0 ) $OptionArrayList .= "<option value='".$row->id."' class='option2'>".$row->typename."(".$channels[$row->channeltype].")</option>\r\n";
			else $OptionArrayList .= "<option value='".$row->id."' class='option3'>".$row->typename."</option>\r\n";
			$OptionArrayList .= $sonCats;
		}
		else
		{
			if($row->ispart==0 && (!empty($channeltype) && $row->channeltype==$channeltype) )
			{
				$OptionArrayList .= "<option value='".$row->id."' class='option3'>".$row->typename."</option>\r\n";
			}
		}
	}
	return $OptionArrayList;
}
function LogicGetOptionArray($id,$step,$channeltype,&$dsql, &$sonCats)
{
	global $OptionArrayList, $channels, $cfg_admin_channel, $admin_catalogs;
	$dsql->SetQuery("Select id,typename,ispart,channeltype From `#@__arctype` where reid='".$id."' And ispart<>2 order by sortrank asc");
	$dsql->Execute($id);
	while($row=$dsql->GetObject($id))
	{
		if($cfg_admin_channel != 'all' && !in_array($row->id, $admin_catalogs))
		{
			continue;
		}
		if($row->channeltype==$channeltype && $row->ispart==1)
		{
			$sonCats .= "<option value='".$row->id."' class='option1'>$step".$row->typename."</option>\r\n";
		}
		else if( ($row->channeltype==$channeltype && $row->ispart==0) || empty($channeltype) )
		{
			$sonCats .= "<option value='".$row->id."' class='option3'>$step".$row->typename."</option>\r\n";
		}
		LogicGetOptionArray($row->id,$step.'─',$channeltype,$dsql, $sonCats);
	}
}
?>