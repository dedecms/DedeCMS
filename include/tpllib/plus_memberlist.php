<?php
if(!defined('DEDEINC'))
{
   exit("Request Error!");
}
//orderby = logintime(login new) or mid(register new)
function plus_memberlist(&$atts,&$refObj,&$fields)
{
	  global $dsql,$_vars;
    $attlist = "row=6,iscommend=0,orderby=logintime,signlen=50";
    FillAtts($atts,$attlist);
    FillFields($atts,$fields,$refObj);
	  extract($atts, EXTR_OVERWRITE);

    $rearray = array();

    $wheresql = ' where mb.spacesta > -1 AND mb.matt != 10';

    if($iscommend > 0) $wheresql .= " And  mb.matt='$iscommend' ";

		$sql = "Select mb.*,ms.spacename,ms.sign From `#@__member` mb
		left join `#@__member_space` ms on ms.mid = mb.mid $wheresql order by mb.{$orderby} desc limit 0,$row ";

		$dsql->Execute('mb',$sql);
		while($row = $dsql->GetArray('mb'))
        {
		  $row['spaceurl'] = $GLOBALS['cfg_basehost'].'/member/index.php?uid='.$row['userid'];
		  if(empty($row['face'])) {
			$row['face']=($row['sex']=='Ů')? $GLOBALS['cfg_memberurl'].'/templets/images/dfgirl.png' : $GLOBALS['cfg_memberurl'].'/templets/images/dfboy.png';
		  }
		  $rearray[] = $row;
		}
		return $rearray;
}
?>