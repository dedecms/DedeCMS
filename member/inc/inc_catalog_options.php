<?php
require_once(dirname(__FILE__)."/../../include/pub_dedetag.php"); 
require_once(dirname(__FILE__)."/../../include/inc_custom_fields.php");

if(empty($selv)) $selv = '0';

function GetOptionList($selid=0,$userCatalog=0,$channeltype=0)
{
    global $OptionArrayList,$channels,$dsql;
    
    if(!is_object($dsql)) $dsql = new DedeSql(false);
    
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
        LogicGetOptionArray($row->ID,"─",$channeltype,$dsql);
    }
     
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
       LogicGetOptionArray($row->ID,$step."─",$channeltype,$dsql);
    }
}


function GetTypeidSelMember($fname,$fv,$fbt,$channelid,$selv='0',$selname='请选择...',$opall=0){
  global $cfg_mainsite,$cfg_phpurl,$cfg_cmspath;
  $rstr  = "<script language='javascript' src='{$cfg_phpurl}/plus.js'></script>\r\n";
  $rstr .= "<input type=\"hidden\" name=\"$fv\" value=\"$selv\">\r\n";
	$rstr .= "<input type=\"button\" name=\"$fbt\" value=\"$selname\"";
	$rstr .= " style=\"height:21px;width:150px;border:0px;background-image:url({$cfg_phpurl}/img/ctbg.gif);padding-top:2px; background-color: transparent\"";
  $rstr .= " onClick=\"SelectCatalog('$fname','$fv','$fbt',$channelid,'$opall','{$cfg_mainsite}{$cfg_cmspath}/include');\">\r\n";
	return $rstr;
}


//获得一个附加表单
//-----------------------------
function GetFormItemA($ctag)
{
	return GetFormItem($ctag,'member');
}
//---------------------------
//处理不同类型的数据
//---------------------------
function GetFieldValueA($dvalue,$dtype,$aid=0,$job='add',$addvar='')
{
	return GetFieldValue($dvalue,$dtype,$aid,$job,$addvar,'member');
}
//获得带值的表单(编辑时用)
//-----------------------------
function GetFormItemValueA($ctag,$fvalue)
{
	return GetFormItemValue($ctag,$fvalue,'member');
}

//载入自定义表单(用于发布)
function PrintAutoFieldsAdd(&$fieldset,$loadtype='autofield')
{
   global $cfg_cookie_encode;
   $dtp = new DedeTagParse();
	 $dtp->SetNameSpace("field","<",">");
   $dtp->LoadSource($fieldset);
   $dede_addonfields = "";
   if(is_array($dtp->CTags))
   {
      foreach($dtp->CTags as $tid=>$ctag)
	  {
        	if($ctag->GetAtt('notsend') !='1' && ($loadtype!='autofield' || ($loadtype=='autofield' && $ctag->GetAtt('autofield')==1)) )
        	{
        			$dede_addonfields .= ( $dede_addonfields=='' ? $ctag->GetName().",".$ctag->GetAtt('type') : ";".$ctag->GetName().",".$ctag->GetAtt('type') );
              echo  GetFormItemA($ctag);
        	}
      }
  }
  echo "<input type='hidden' name='dede_addonfields' value=\"".$dede_addonfields."\" />\r\n";
  echo "<input type='hidden' name='dede_fieldshash' value=\"".md5($dede_addonfields.$cfg_cookie_encode)."\" />\r\n";
}

//载入自定义表单(用于编辑)
function PrintAutoFieldsEdit(&$fieldset,&$fieldValues,$loadtype='autofield')
{
   global $cfg_cookie_encode;
   $dtp = new DedeTagParse();
	 $dtp->SetNameSpace("field","<",">");
   $dtp->LoadSource($fieldset);
   $dede_addonfields = "";
   if(is_array($dtp->CTags))
   {
      foreach($dtp->CTags as $tid=>$ctag)
			{
        if($ctag->GetAtt('notsend') !='1' && ($loadtype!='autofield' || ($loadtype=='autofield' && $ctag->GetAtt('autofield')==1)) )
        {
             $dede_addonfields .= ( $dede_addonfields=='' ? $ctag->GetName().",".$ctag->GetAtt('type') : ";".$ctag->GetName().",".$ctag->GetAtt('type') );
             echo GetFormItemValueA($ctag,$fieldValues[$ctag->GetName()]);
        }
      }
  }
  echo "<input type='hidden' name='dede_addonfields' value=\"".$dede_addonfields."\" />\r\n";
  echo "<input type='hidden' name='dede_fieldshash' value=\"".md5($dede_addonfields.$cfg_cookie_encode)."\" />\r\n";
}

?>