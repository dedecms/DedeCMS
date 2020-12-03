<?php
function GetOptionList($selid=0,$userCatalog=0,$channeltype=0)
{
	global $OptionArrayList,$channels,$dsql;

	$dsql->SetQuery("Select id,typename From `#@__channeltype` ");
	$dsql->Execute();
	$channels = Array();
	while($row = $dsql->GetObject()) $channels[$row->id] = $row->typename;

	$OptionArrayList = "";

	//当前选中的栏目
	if($selid > 0){
		$row = $dsql->GetOne("Select id,typename,ispart,channeltype From `#@__arctype` where id='$selid'");
		if($row['ispart']==1) $OptionArrayList .= "<option value='".$row['id']."' class='option1' selected='1'>".$row['typename']."(封面频道)</option>\r\n";
		else $OptionArrayList .= "<option value='".$row['id']."' selected>".$row['typename']."</option>\r\n";
	}

	//是否限定用户管理的栏目
	if($userCatalog>0)
	{ $query = "Select id,typename,ispart,channeltype From `#@__arctype` where ispart<>2 And id='$userCatalog' "; }
	else
	{ $query = "Select id,typename,ispart,channeltype From `#@__arctype` where ispart<>2 And reid=0 order by sortrank asc "; }

	$dsql->SetQuery($query);
	$dsql->Execute();

	while($row=$dsql->GetObject())
	{
		if($row->ispart==1) $OptionArrayList .= "<option value='".$row->id."' class='option1'>".$row->typename."(封面频道)</option>\r\n";
		else if($row->ispart==2) $OptionArrayList .="";
		else if($row->channeltype!=$channeltype && $channeltype!=0) $OptionArrayList .= "<option value='".$row->id."' class='option2'>".$row->typename."(".$channels[$row->channeltype].")</option>\r\n";
		else $OptionArrayList .= "<option value='".$row->id."' class='option3'>".$row->typename."</option>\r\n";
		LogicGetOptionArray($row->id,"─",$channeltype,$dsql);
	}


	return $OptionArrayList;
}
function LogicGetOptionArray($id,$step,$channeltype,&$dsql)
{
	global $OptionArrayList,$channels;
	$dsql->SetQuery("Select id,typename,ispart,channeltype From `#@__arctype` where reid='".$id."' And ispart<>2 order by sortrank asc");
	$dsql->Execute($id);
	while($row=$dsql->GetObject($id))
	{
		if($row->ispart==1) $OptionArrayList .= "<option value='".$row->id."' class='option1'>$step".$row->typename."(封面频道)</option>\r\n";
		else if($row->ispart==2) $OptionArrayList .="";
		else if($row->channeltype!=$channeltype && $channeltype!=0) $OptionArrayList .='';
		else $OptionArrayList .= "<option value='".$row->id."' class='option3'>$step".$row->typename."</option>\r\n";
		LogicGetOptionArray($row->id,$step."─",$channeltype,$dsql);
	}
}
?>