<?php 
require_once(dirname(__FILE__)."/../../include/inc_channel_unit_functions.php");
$GLOBALS['adminCats'] = array();
$GLOBALS['suserCatalog'] = '';
function GetOptionList($selid=0,$userCatalog=0,$channeltype=0)
{
    global $OptionArrayList,$channels,$adminCats,$adminCatstrs,$suserCatalog;
    $suserCatalog = $userCatalog;
    $dsql = new DedeSql(false);
    //读取频道模型信息
    $dsql->SetQuery("Select ID,typename From #@__channeltype ");
    $dsql->Execute();
    $channels = Array();
    while($row = $dsql->GetObject()) $channels[$row->ID] = $row->typename;
    
    $OptionArrayList = "";
    
    $adminCats = array();
    if(!empty($userCatalog) && $userCatalog!='-1')
    {
    	 $adminCats = explode(',',$userCatalog);
    	 $adminCatstrs = $userCatalog;
    }

    if($selid==0)
    {	
        $query = "Select ID,typename,ispart,channeltype From #@__arctype where ispart<2 And reID=0 order by sortrank asc ";
        $dsql->SetQuery($query);
        $dsql->Execute();
       while($row=$dsql->GetObject())
       {
          if(TestHasChannel($row->ID,$channeltype)==0) continue;
          if( TestAdmin() || $userCatalog==-1 )
          {
          	  if($row->ispart==1) $OptionArrayList .= "<option value='".$row->ID."' class='option1'>".$row->typename."(封面频道)</option>\r\n";
              else if($row->ispart==2) $OptionArrayList .= "";
              else if($row->channeltype!=$channeltype) $OptionArrayList .= "<option value='".$row->ID."' class='option2'>".$row->typename."(".$channels[$row->channeltype].")</option>\r\n";
              else $OptionArrayList .= "<option value='".$row->ID."' class='option3'>".$row->typename."</option>\r\n";
          	  LogicGetOptionArray($row->ID,"─",$channeltype,$dsql);
          }else
          {
             if(in_array($row->ID,$adminCats))
             {
             	  if($row->ispart==1) $OptionArrayList .= "<option value='".$row->ID."' class='option1'>".$row->typename."(封面频道)</option>\r\n";
                else if($row->ispart==2) $OptionArrayList .= "";
                else if($row->channeltype!=$channeltype) $OptionArrayList .= "<option value='".$row->ID."' class='option2'>".$row->typename."(".$channels[$row->channeltype].")</option>\r\n";
                else $OptionArrayList .= "<option value='".$row->ID."' class='option3'>".$row->typename."</option>\r\n";
                LogicGetOptionArray($row->ID,"─",$channeltype,$dsql,false);
             }else
             {
             	 $haspurcat = false;
             	 $query = "Select ID From #@__arctype where ispart<2 And reID={$row->ID} order by sortrank asc ";
               $dsql->Execute('sel'.$row->ID,$query);
               while($nrow = $dsql->GetObject('sel'.$row->ID)){
          	      if(in_array($nrow->ID,$adminCats)){ $haspurcat=true; break; }
               }
               if($haspurcat){
             	    $OptionArrayList .= "<option value='".$row->ID."' class='option1'>".$row->typename."(没权限)</option>\r\n";
             	    LogicGetOptionArray($row->ID,"─",$channeltype,$dsql);
             	 }
             }
          }
       }
    }else
    {
    	   $row = $dsql->GetOne("Select ID,typename,ispart,channeltype From #@__arctype where ID='$selid'");
    	   $channeltype = $row['channeltype'];
    	   if($row['ispart']==1) $OptionArrayList .= "<option value='$selid' class='option1' selected>".$row['typename']."(封面频道)</option>\r\n";
         else $OptionArrayList .= "<option value='$selid' class='option3' selected>".$row['typename']."</option>\r\n";
         LogicGetOptionArray($selid,"─",$channeltype,$dsql,false);
    }
    return $OptionArrayList; 
	}
	
	 
	function LogicGetOptionArray($ID,$step,$channeltype,$dsql,$testpur=true)
	{
		global $OptionArrayList,$channels,$adminCats,$suserCatalog;
		$dsql->SetQuery("Select ID,typename,ispart,channeltype From #@__arctype where reID='".$ID."' And ispart<2 order by sortrank asc");
		$dsql->Execute($ID);
		while($row=$dsql->GetObject($ID))
		{
       if($suserCatalog!=-1 && $testpur && !TestAdmin() && !in_array($row->ID,$adminCats)) continue;
       if($row->ispart==1) $OptionArrayList .= "<option value='".$row->ID."' class='option1'>$step".$row->typename."(封面频道)</option>\r\n";
       else if($row->ispart==2) $OptionArrayList .="";
       else if($row->channeltype!=$channeltype) $OptionArrayList .="";
       else $OptionArrayList .= "<option value='".$row->ID."' class='option3'>$step".$row->typename."</option>\r\n";
       LogicGetOptionArray($row->ID,$step."─",$channeltype,$dsql,false);
    }
	}
?>