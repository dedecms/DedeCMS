<?php
if(!defined('DEDEINC')) exit('Request Error!');
function lib_autochannel(&$ctag,&$refObj)
{
	global $dsql;

	$attlist='partsort|0,typeid=-1';
	FillAttsDefault($ctag->CAttribute->Items,$attlist);
	extract($ctag->CAttribute->Items, EXTR_SKIP);

	$innertext = trim($ctag->GetInnerText());
	$topid = $typeid;
	$sortid = $partsort;

	if($topid=='-1' || $topid=='')
	{		
		$topid = ( isset($refObj->TypeLink->TypeInfos['id']) ? $refObj->TypeLink->TypeInfos['id'] : 0);
	}
	
	if(empty($sortid)) $sortid = 1;
	$getstart = $sortid - 1;

	$row = $dsql->GetOne("Select id,typename From `#@__arctype` where reid='{$topid}' And 
	                      ispart<2 And ishidden<>'1' order by sortrank asc limit $getstart,1");
	                      
	if(!is_array($row) ) return '';
	else $typeid = $row['id'];
	
	if(trim($innertext)=='') $innertext = GetSysTemplets('part_autochannel.htm');
	
	$row = $dsql->GetOne("Select id,typedir,isdefault,defaultname,ispart,namerule2,typename,moresite,siteurl,sitepath 
	                      From `#@__arctype` where id='$typeid' ");
	if(!is_array($row)) return '';

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