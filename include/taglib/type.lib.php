<?php

if(!defined('DEDEINC')) exit('Request Error!');

function lib_type(&$ctag,&$refObj)
{
	global $dsql,$envs;

	$attlist='typeid|0';
	FillAttsDefault($ctag->CAttribute->Items,$attlist);
	extract($ctag->CAttribute->Items, EXTR_SKIP);
	$innertext = trim($ctag->GetInnerText());

	if($typeid==0) {
		$typeid = ( isset($refObj->TypeLink->TypeInfos['id']) ? $refObj->TypeLink->TypeInfos['id'] : $envs['typeid'] );
	}

  if(empty($typeid)) return '';

	$row = $dsql->GetOne("Select id,typedir,isdefault,defaultname,ispart,namerule2,typename,moresite,siteurl,sitepath 
	                     From `#@__arctype` where id='$typeid' ");
	if(!is_array($row)) return '';
	if(trim($innertext)=='') $innertext = GetSysTemplets("part_type_list.htm");
	
	$dtp = new DedeTagParse();
	$dtp->SetNameSpace('field','[',']');
	$dtp->LoadSource($innertext);
	if(!is_array($dtp->CTags))
	{
		unset($dtp);
		return '';
	}
	else
	{
		$row['typelink'] = GetTypeUrl($row['id'],MfTypedir($row['typedir']),$row['isdefault'],
		                    $row['defaultname'],$row['ispart'],$row['namerule2'],$row['siteurl'],$row['sitepath']);
		foreach($dtp->CTags as $tagid=>$ctag)
		{
			if(isset($row[$ctag->GetName()])) $dtp->Assign($tagid,$row[$ctag->GetName()]);
		}
		$revalue = $dtp->GetResult();
		unset($dtp);
		return $revalue;
	}
}
?>