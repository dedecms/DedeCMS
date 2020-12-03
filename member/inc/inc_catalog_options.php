<?
function GetOptionList($selid=0,$userCatalog=0,$channeltype=0)
{
    global $OptionArrayList,$channels,$dsql;
    
    $isopenSql = false;
    if(!is_object($dsql)){
    	$dsql = new DedeSql(false);
    	$isopenSql = true;
    }
    
    $dsql->SetQuery("Select ID,typename From #@__channeltype ");
    $dsql->Execute();
    $channels = Array();
    while($row = $dsql->GetObject()) $channels[$row->ID] = $row->typename;
    
    $OptionArrayList = "";
    
    $query = "Select ID,typename,ispart,channeltype,issend From #@__arctype where ispart<>2 And reID=0 order by sortrank asc ";
      
    $dsql->SetQuery($query);
    $dsql->Execute();
    	
    while($row=$dsql->GetObject())
    {
        if($row->channeltype==$channeltype && $row->issend==1){
        	 if($row->ispart==0) $OptionArrayList .= "<option value='".$row->ID."' class='option3'>".$row->typename."</option>\r\n";
           else if($row->ispart==1) $OptionArrayList .= "<option value='".$row->ID."' class='option2'>".$row->typename."</option>\r\n";
        }
        LogicGetOptionArray($row->ID,"йд",$channeltype,$dsql);
    }
    if($isopenSql) $dsql->Close();
     
    return $OptionArrayList; 
}

function LogicGetOptionArray($ID,$step,$channeltype,$dsql)
{
		global $OptionArrayList,$channels;
		$dsql->SetQuery("Select ID,typename,ispart,channeltype,issend From #@__arctype where reID='".$ID."' And ispart<>2 order by sortrank asc");
		$dsql->Execute($ID);
		while($row=$dsql->GetObject($ID)){
       if($row->channeltype==$channeltype && $row->issend==1){
        	 if($row->ispart==0) $OptionArrayList .= "<option value='".$row->ID."' class='option3'>$step".$row->typename."</option>\r\n";
           else if($row->ispart==1) $OptionArrayList .= "<option value='".$row->ID."' class='option2'>$step".$row->typename."</option>\r\n";
        }
       LogicGetOptionArray($row->ID,$step."йд",$channeltype,$dsql);
    }
}
?>