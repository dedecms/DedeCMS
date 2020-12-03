<?php

if(!defined('DEDEINC')) exit('Request Error!');

function lib_group(&$ctag,&$refObj)
{
	global $dsql, $envs, $cfg_dbprefix, $cfg_cmsurl;
	//属性处理
	$attlist="row|6,orderby|threads,titlelen|30";
	FillAttsDefault($ctag->CAttribute->Items,$attlist);
	extract($ctag->CAttribute->Items, EXTR_SKIP);

	if( !$dsql->IsTable("{$cfg_dbprefix}groups") ) return '没安装圈子模块';
	
	if(!ereg("/$", $cfg_cmsurl)) $cfg_group_url = $cfg_cmsurl.'/group';
	else $cfg_group_url = $cfg_cmsurl.'group';
	
	$innertext = $ctag->GetInnerText();
	if(trim($innertext)=='') $innertext = GetSysTemplets("groups.htm");
	
	$list = '';
	$dsql->SetQuery("SELECT groupimg,groupid,groupname FROM `#@__groups` WHERE ishidden=0 ORDER BY $orderby DESC LIMIT 0,{$row}");
  $dsql->Execute();
  $ctp = new DedeTagParse();
  $ctp->SetNameSpace('field', '[', ']');
  while($rs = $dsql->GetArray())
  {
  	  $ctp->LoadSource($innertext);
  	  $rs['groupname'] = cn_substr($rs['groupname'], $titlelen);
  	  $rs['url'] = $cfg_group_url."/group.php?id={$rs['groupid']}";
  	  $rs['icon']  = $rs['groupimg'];
  	  foreach($ctp->CTags as $tagid=>$ctag)
  	  {
		    if( !empty($rs[strtolower($ctag->GetName())]) ) {
		    	$ctp->Assign($tagid,$rs[$ctag->GetName()]);
		    }
		  }
		  $list .= $ctp->GetResult();
  }
	return $list;
}

?>