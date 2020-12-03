<?php

//获得字段创建信息
function GetFieldMake($dtype,$fieldname,$dfvalue,$mxlen)
{
	$fields = array();
	if($dtype=="int"||$dtype=="datetime"){
    	if($dfvalue=="" || ereg("[^0-9-]",$dfvalue)){ $dfvalue = 0; }
    	$fields[0] = " `$fieldname` int(11) NOT NULL default '$dfvalue';";
    	$fields[1] = "int(11)";
  }else if($dtype=="float"){
      if($dfvalue=="" || ereg("[^0-9\.-]",$dfvalue)){ $dfvalue = 0; }
      $fields[0] = " `$fieldname` float NOT NULL default '$dfvalue';";
    	$fields[1] = "float";
  }else if($dtype=="img"||$dtype=="media"||$dtype=="addon"||$dtype=="imgfile"){
    	if(empty($dfvalue)) $dfvalue = '';
    	if($mxlen=="") $mxlen = 200;
    	if($mxlen > 255) $mxlen = 100;
    	$fields[0] = " `$fieldname` varchar($mxlen) NOT NULL default '$dfvalue';";
    	$fields[1] = "varchar($mxlen)";
  }else if($dtype=="multitext"||$dtype=="htmltext"){
    	$fields[0] = " `$fieldname` mediumtext;";
    	$fields[1] = "mediumtext";
  }else if($dtype=="textdata"){
    	if(empty($dfvalue)) $dfvalue = '';
    	$fields[0] = " `$fieldname` varchar(100) NOT NULL default '';";
    	$fields[1] = "varchar(100)";
  }else if($dtype=="textchar"){
    	if(empty($dfvalue)) $dfvalue = '';
    	$fields[0] = " `$fieldname` char(100) NOT NULL default '$dfvalue';";
    	$fields[1] = "char(100)";
  }
  else{
    	if(empty($dfvalue)) $dfvalue = '';
    	if(empty($mxlen)) $mxlen = 100;
    	if($mxlen > 255) $mxlen = 250;
    	$fields[0] = " `$fieldname` varchar($mxlen) NOT NULL default '$dfvalue';";
    	$fields[1] = "varchar($mxlen)";
  }
  return $fields;
}

//获得模型的列表字段
function GetAddFieldList(&$dtp,&$oksetting)
{
	$oklist = '';
	$dtp->SetNameSpace("field","<",">");
  $dtp->LoadSource($oksetting);
  if(is_array($dtp->CTags))
  {
	  foreach($dtp->CTags as $tagid=>$ctag)
	  {
	  	if($ctag->GetAtt('islist')==1){
	  		$oklist .= ($oklist=='' ? strtolower($ctag->GetName()) : ','.strtolower($ctag->GetName()) );
	  	}
	  }
	}
	return $oklist;
}

?>