<?
//获得一个附加表单
//-----------------------------
function GetFormItem($ctag)
{
	$fieldname = $ctag->GetName();
	$formitem = "
		<table width=\"600\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
       <tr> 
        <td width=\"80\">~name~</td>
        <td width=\"520\">~form~</td>
       </tr>
    </table>\r\n";
	$innertext = trim($ctag->GetInnerText());
	if($innertext!=""){
		 $formitem = str_replace("~name~",$ctag->GetAtt('itemname'),$formitem);
		 $formitem = str_replace("~form~",$innertext,$formitem);
		 return $formitem;
	}
	
	if($ctag->GetAtt("type")=="htmltext")
	{
		$formitem = "";
		$formitem .= "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\"><tr><td width=\"80\">".$ctag->GetAtt('itemname')."</td><td>";
		$formitem .= GetEditor($fieldname,'',350,'Small','string');
		$formitem .= "</td></tr></table>\r\n";
		return $formitem;
	}
	else if($ctag->GetAtt("type")=="multitext")
	{
		$innertext = "<textarea name='$fieldname' id='$fieldname' style='width:100%;height:80'></textarea>\r\n";
		$formitem = str_replace("~name~",$ctag->GetAtt('itemname'),$formitem);
		$formitem = str_replace("~form~",$innertext,$formitem);
		return $formitem;
	}
	else if($ctag->GetAtt("type")=="datetime")
	{
		$nowtime = GetDateTimeMk(time());
		$innertext = "<input name=\"$fieldname\" value=\"$nowtime\" type=\"text\" id=\"$fieldname\" style=\"width:200\">";
		$innertext .= "<input name=\"selPubtime\" type=\"button\" id=\"selkeyword\" value=\"选择\" onClick=\"showCalendar('$fieldname', '%Y-%m-%d %H:%M:00', '24');\">";
		$formitem = str_replace("~name~",$ctag->GetAtt('itemname'),$formitem);
		$formitem = str_replace("~form~",$innertext,$formitem);
		return $formitem;
	}
	else if($ctag->GetAtt("type")=="img")
	{
		$innertext = "<input type='text' name='$fieldname' id='$fieldname' style='width:300'><input name='".$fieldname."_bt' type='button' value='浏览...' onClick=\"SelectImage('form1.$fieldname','big')\">\r\n";
		$formitem = str_replace("~name~",$ctag->GetAtt('itemname'),$formitem);
		$formitem = str_replace("~form~",$innertext,$formitem);
		return $formitem;
	}
	else if($ctag->GetAtt("type")=="media")
	{
		$innertext = "<input type='text' name='$fieldname' id='$fieldname' style='width:300'><input name='".$fieldname."_bt' type='button' value='浏览...' onClick=\"SelectMedia('form1.$fieldname')\">\r\n";
		$formitem = str_replace("~name~",$ctag->GetAtt('itemname'),$formitem);
		$formitem = str_replace("~form~",$innertext,$formitem);
		return $formitem;
	}
	else if($ctag->GetAtt("type")=="addon")
	{
		$innertext = "<input type='text' name='$fieldname' id='$fieldname' style='width:300'><input name='".$fieldname."_bt' type='button' value='浏览...' onClick=\"SelectSoft('form1.$fieldname')\">\r\n";
		$formitem = str_replace("~name~",$ctag->GetAtt('itemname'),$formitem);
		$formitem = str_replace("~form~",$innertext,$formitem);
		return $formitem;
	}
	else
	{
		if($ctag->GetAtt('default')!="") $dfvalue = $ctag->GetAtt('default');
		else $dfvalue = "";
		$innertext = "<input type='text' name='$fieldname' id='$fieldname' style='width:200' value='$dfvalue'>\r\n";
		$formitem = str_replace("~name~",$ctag->GetAtt('itemname'),$formitem);
		$formitem = str_replace("~form~",$innertext,$formitem);
		return $formitem;
	}
}
//---------------------------
//处理不同类型的数据
//---------------------------
function GetFieldValue($dvalue,$dtype)
{
	if($dtype=="int")
	{
		$dvalue = trim(ereg_replace("[^0-9]","",$dvalue));
		if($dvalue=="") $dvalue = 0;
		return $dvalue;
	}
	else if($dtype=="float")
	{
		$dvalue = trim(ereg_replace("[^0-9\.]","",$dvalue));
		if($dvalue=="") $dvalue = 0;
		return $dvalue;
	}
	else if($dtype=="datetime")
	{
		return GetMkTime($dvalue);
	}
	else if($dtype=="img")
	{
		$iurl = stripslashes($dvalue);
    if(trim($iurl)=="") return "";
    $iurl = trim(str_replace($GLOBALS['cfg_basehost'],"",$iurl));
    $imgurl = "{dede:img text='' width='' height=''} ".$iurl." {/dede:img}";
    if(eregi("^http://",$iurl) && $GLOBALS['isUrlOpen']) //远程图片
    {
       $reimgs = "";
       if($isUrlOpen)
       {
	       $reimgs = GetRemoteImage($iurl,$GLOBALS['adminID']);
	       if(is_array($reimgs)){
		        $imgurl = "{dede:img text='' width='".$reimgs[1]."' height='".$reimgs[2]."'} ".$reimgs[0]." {/dede:img}";
	       }
	     }
	     else
	     {
	     	 $imgurl = "{dede:img text='' width='' height=''} ".$iurl." {/dede:img}";
	     }
    }
    else if($iurl!="") //站内图片
    {
	     $imgfile = $GLOBALS['cfg_basedir'].$iurl;
	     if(is_file($imgfile)){
		     $info = "";
		     $imginfos = GetImageSize($imgfile,$info);
		     $imgurl = "{dede:img text='' width='".$imginfos[0]."' height='".$imginfos[1]."'} $iurl {/dede:img}";
	     }
    }
    return addslashes($imgurl);
	}
	else{
		return $dvalue;
	}
}
//获得带值的表单(编辑时用)
//-----------------------------
function GetFormItemValue($ctag,$fvalue)
{
	$fieldname = $ctag->GetName();
	$formitem = "
		<table width=\"600\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
       <tr> 
        <td width=\"80\">~name~</td>
        <td width=\"520\">~form~</td>
       </tr>
    </table>\r\n";
	if($ctag->GetAtt("type")=="htmltext")
	{
		$formitem .="<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\"><tr><td width=\"80\">".$ctag->GetAtt('itemname')."</td><td>";
		$formitem .= GetEditor($fieldname,$fvalue,350,'Small','string');
		$formitem .="</td></tr></table>\r\n";
		return $formitem;
	}
	else if($ctag->GetAtt("type")=="multitext")
	{
		$innertext = "<textarea name='$fieldname' id='$fieldname' style='width:100%;height:80'>$fvalue</textarea>\r\n";
		$formitem = str_replace("~name~",$ctag->GetAtt('itemname'),$formitem);
		$formitem = str_replace("~form~",$innertext,$formitem);
		return $formitem;
	}
	else if($ctag->GetAtt("type")=="datetime")
	{
		$nowtime = GetDateTimeMk($fvalue);
		$innertext = "<input name=\"$fieldname\" value=\"$nowtime\" type=\"text\" id=\"$fieldname\" style=\"width:200\">";
		$innertext .= "<input name=\"selPubtime\" type=\"button\" id=\"selkeyword\" value=\"选择\" onClick=\"showCalendar('$fieldname', '%Y-%m-%d %H:%M:00', '24');\">";
		$formitem = str_replace("~name~",$ctag->GetAtt('itemname'),$formitem);
		$formitem = str_replace("~form~",$innertext,$formitem);
		return $formitem;
	}
	else if($ctag->GetAtt("type")=="img")
	{
		$ndtp = new DedeTagParse();
    $ndtp->LoadSource($fvalue);
    if(!is_array($ndtp->CTags)){
    	$ndtp->Clear();
    	$fvalue =  "";
    }
    $ntag = $ndtp->GetTag("img");
    $fvalue = trim($ntag->GetInnerText());
		$innertext = "<input type='text' name='$fieldname' value='$fvalue' id='$fieldname' style='width:300'><input name='".$fieldname."_bt' type='button' value='浏览...' onClick=\"SelectImage('form1.$fieldname','big')\">\r\n";
		$formitem = str_replace("~name~",$ctag->GetAtt('itemname'),$formitem);
		$formitem = str_replace("~form~",$innertext,$formitem);
		return $formitem;
	}
	else if($ctag->GetAtt("type")=="media")
	{
		$innertext = "<input type='text' name='$fieldname' value='$fvalue' id='$fieldname' style='width:300'><input name='".$fieldname."_bt' type='button' value='浏览...' onClick=\"SelectMedia('form1.$fieldname')\">\r\n";
		$formitem = str_replace("~name~",$ctag->GetAtt('itemname'),$formitem);
		$formitem = str_replace("~form~",$innertext,$formitem);
		return $formitem;
	}
	else if($ctag->GetAtt("type")=="addon")
	{
		$innertext = "<input type='text' name='$fieldname' id='$fieldname' value='$fvalue' style='width:300'><input name='".$fieldname."_bt' type='button' value='浏览...' onClick=\"SelectSoft('form1.$fieldname')\">\r\n";
		$formitem = str_replace("~name~",$ctag->GetAtt('itemname'),$formitem);
		$formitem = str_replace("~form~",$innertext,$formitem);
		return $formitem;
	}
	else
	{
		$innertext = "<input type='text' name='$fieldname' id='$fieldname' style='width:200' value='$fvalue'>\r\n";
		$formitem = str_replace("~name~",$ctag->GetAtt('itemname'),$formitem);
		$formitem = str_replace("~form~",$innertext,$formitem);
		return $formitem;
	}
}
?>