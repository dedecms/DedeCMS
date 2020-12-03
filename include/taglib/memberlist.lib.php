<?php
if(!defined('DEDEINC'))
{
	exit("Request Error!");
}
//orderby = logintime(login new) or mid(register new)
function lib_memberlist(&$ctag,&$refObj)
{
	global $dsql,$sqlCt;
	$attlist="row|6,iscommend|0,orderby|logintime,signlen|50";
	FillAttsDefault($ctag->CAttribute->Items,$attlist);
	extract($ctag->CAttribute->Items, EXTR_SKIP);

	$revalue = '';
	$innerText = trim($ctag->GetInnerText());
	if(empty($innerText)) $innerText = GetSysTemplets('memberlist.htm');

	$wheresql = ' where mb.spacesta>-1 And mb.matt<10 ';

	if($iscommend > 0) $wheresql .= " And  mb.matt='$iscommend' ";

	$sql = "Select mb.*,ms.spacename,ms.sign From `#@__member` mb
		left join `#@__member_space` ms on ms.mid = mb.mid
		$wheresql order by mb.{$orderby} desc limit 0,$row ";
	
	$ctp = new DedeTagParse();
	$ctp->SetNameSpace('field','[',']');
	$ctp->LoadSource($innerText);

	$dsql->Execute('mb',$sql);
	while($row = $dsql->GetArray('mb'))
	{
		$row['spaceurl'] = $GLOBALS['cfg_basehost'].'/member/index.php?uid='.$row['userid'];
		if(empty($row['face'])){
			$row['face']=($row['sex']=='女')? $GLOBALS['cfg_memberurl'].'/templets/images/dfgirl.png' : $GLOBALS['cfg_memberurl'].'/templets/images/dfboy.png';
		}
		foreach($ctp->CTags as $tagid=>$ctag){
			if(isset($row[$ctag->GetName()])){ $ctp->Assign($tagid,$row[$ctag->GetName()]); }
		}
		$revalue .= $ctp->GetResult();
	}
	
	return $revalue;
}
?>