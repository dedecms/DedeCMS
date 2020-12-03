<?php
if(!defined('DEDEINC'))
{
	exit("Request Error!");
}

function lib_memberinfos(&$ctag,&$refObj)
{
	global $dsql,$sqlCt;
	$attlist="mid|0";
	FillAttsDefault($ctag->CAttribute->Items,$attlist);
	extract($ctag->CAttribute->Items, EXTR_SKIP);
	
	if(empty($mid))
	{
		if(!empty($refObj->Fields['mid'])) $mid =  $refObj->Fields['mid'];
		else $mid = 1;
	}
	else
	{
			$mid = intval($mid);
	}

	$revalue = '';
	$innerText = trim($ctag->GetInnerText());
	if(empty($innerText)) $innerText = GetSysTemplets('memberinfos.htm');

	$sql = "Select mb.*,ms.spacename,ms.sign,ar.membername as rankname From `#@__member` mb
		left join `#@__member_space` ms on ms.mid = mb.mid 
		left join `#@__arcrank` ar on ar.rank = mb.rank
		where mb.mid='{$mid}' limit 0,1 ";

	$ctp = new DedeTagParse();
	$ctp->SetNameSpace('field','[',']');
	$ctp->LoadSource($innerText);

	$dsql->Execute('mb',$sql);
	while($row = $dsql->GetArray('mb'))
	{
		if($row['matt']==10) return '';
		$row['spaceurl'] = $GLOBALS['cfg_basehost'].'/member/index.php?uid='.$row['userid'];
		if(empty($row['face'])) {
			$row['face']=($row['sex']=='女')?  $GLOBALS['cfg_memberurl'].'/templets/images/dfgirl.png' : $GLOBALS['cfg_memberurl'].'/templets/images/dfboy.png';
		}
		foreach($ctp->CTags as $tagid=>$ctag)
		{
			if(isset($row[$ctag->GetName()])){ $ctp->Assign($tagid,$row[$ctag->GetName()]); }
		}
		$revalue .= $ctp->GetResult();
	}
	return $revalue;
}
?>