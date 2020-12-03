<?php
if(!defined('DEDEMEMBER'))
{
	exit("dedecms");
}

function GetOptionList($selid=0,$channeltype=0)
{
	global $OptionArrayList,$channels,$dsql;
	$dsql->SetQuery("Select id,typename From `#@__channeltype` ");
	$dsql->Execute();
	$channels = Array();
	while($row = $dsql->GetObject())
	{
		$channels[$row->id] = $row->typename;
	}
	$OptionArrayList = "";
	$query = "Select id,typename,ispart,channeltype,issend From `#@__arctype` where ispart<2 And reid=0 order by sortrank asc ";
	$dsql->SetQuery($query);
	$dsql->Execute();
	$selected = '';
	while($row=$dsql->GetObject())
	{
		if($selid==$row->id)
		{
			$selected = " selected='$selected'";
		}
		if($row->channeltype==$channeltype && $row->issend==1)
		{
			if($row->ispart==0)
			{
				$OptionArrayList .= "<option value='".$row->id."' class='option3'{$selected}>".$row->typename."</option>\r\n";
			}
			else if($row->ispart==1)
			{
				$OptionArrayList .= "<option value='".$row->id."' class='option2'{$selected}>".$row->typename."</option>\r\n";
			}
		}
		$selected = '';
		LogicGetOptionArray($row->id,"─",$channeltype,$selid);
	}
	return $OptionArrayList;
}

function LogicGetOptionArray($id,$step,$channeltype,$selid=0)
{
	global $OptionArrayList,$channels,$dsql;
	$selected = '';
	$dsql->SetQuery("Select id,typename,ispart,channeltype,issend From `#@__arctype` where reid='".$id."' And ispart<2 order by sortrank asc");
	$dsql->Execute($id);
	while($row=$dsql->GetObject($id))
	{
		if($selid==$row->id)
		{
			$selected = " selected='$selected'";
		}
		if($row->channeltype==$channeltype && $row->issend==1)
		{
			if($row->ispart==0)
			{
				$OptionArrayList .= "<option value='".$row->id."' class='option3'{$selected}>$step".$row->typename."</option>\r\n";
			}
			else if($row->ispart==1)
			{
				$OptionArrayList .= "<option value='".$row->id."' class='option2'{$selected}>$step".$row->typename."</option>\r\n";
			}
		}
		$selected = '';
		LogicGetOptionArray($row->id,$step."─",$channeltype,$selid);
	}
}

function classification($mid, $mtypeid = 0, $channelid=1)
{
	global $dsql;
	$list = $selected = '';
	$quey = "SELECT * FROM `#@__mtypes` WHERE mid = '$mid' And channelid='$channelid' ;";
	$dsql->SetQuery($quey);
	$dsql->Execute();
	while ($row = $dsql->GetArray())
	{
		if($mtypeid != 0){
			if($mtypeid == $row['mtypeid'])
			{
				$selected = " selected";
			}
		}
		$list .= "<option value='".$row['mtypeid']."' class='option3'{$selected}>".$row['mtypename']."</option>\r\n";
		$selected = '';
	}
	return $list;
}
?>