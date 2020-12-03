<?
function GetOptionList($selid=0,$userCatalog=0,$channeltype=0)
{
    global $OptionArrayList,$channels;
    
    $dsql = new DedeSql(false);
    $dsql->SetQuery("Select ID,typename From #@__channeltype ");
    $dsql->Execute();
    $channels = Array();
    while($row = $dsql->GetObject()) $channels[$row->ID] = $row->typename;
    
    $OptionArrayList = "";
    
    //当前选中的栏目
    if($selid > 0){
    	$row = $dsql->GetOne("Select ID,typename,ispart,channeltype From #@__arctype where ID='$selid'");
    	if($row['ispart']==1) $OptionArrayList .= "<option value='".$row['ID']."' class='option1' selected>".$row['typename']."(封面频道)</option>\r\n";
      else $OptionArrayList .= "<option value='".$row['ID']."' selected>".$row['typename']."</option>\r\n";
    }
    	
    //是否限定用户管理的栏目
    if($userCatalog>0)
    { $query = "Select ID,typename,ispart,channeltype From #@__arctype where ispart<>2 And ID='$userCatalog' "; }
    else
    { $query = "Select ID,typename,ispart,channeltype From #@__arctype where ispart<>2 And reID=0 order by sortrank asc "; }
      
    $dsql->SetQuery($query);
    $dsql->Execute();
    	
    while($row=$dsql->GetObject())
    {
        if($row->ispart==1) $OptionArrayList .= "<option value='".$row->ID."' class='option1'>".$row->typename."(封面频道)</option>\r\n";
        else if($row->ispart==2) $OptionArrayList .="";
        else if($row->channeltype!=$channeltype) $OptionArrayList .= "<option value='".$row->ID."' class='option2'>".$row->typename."(".$channels[$row->channeltype].")</option>\r\n";
        else $OptionArrayList .= "<option value='".$row->ID."' class='option3'>".$row->typename."</option>\r\n";
        LogicGetOptionArray($row->ID,"─",$channeltype,$dsql);
    }
    $dsql->Close();
     
     return $OptionArrayList; 
	}
	function LogicGetOptionArray($ID,$step,$channeltype,$dsql)
	{
		global $OptionArrayList,$channels;
		$dsql->SetQuery("Select ID,typename,ispart,channeltype From #@__arctype where reID='".$ID."' And ispart<>2 order by sortrank asc");
		$dsql->Execute($ID);
		while($row=$dsql->GetObject($ID)){
       if($row->ispart==1) $OptionArrayList .= "<option value='".$row->ID."' class='option1'>$step".$row->typename."(封面频道)</option>\r\n";
       else if($row->ispart==2) $OptionArrayList .="";
       else if($row->channeltype!=$channeltype) $OptionArrayList .="";
       else $OptionArrayList .= "<option value='".$row->ID."' class='option3'>$step".$row->typename."</option>\r\n";
       LogicGetOptionArray($row->ID,$step."─",$channeltype,$dsql);
    }
	}
?>