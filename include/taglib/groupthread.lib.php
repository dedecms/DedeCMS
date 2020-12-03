<?php

if(!defined('DEDEINC')) exit('Request Error!');

function lib_groupthread(&$ctag,&$refObj)
{
	global $dsql, $envs, $cfg_dbprefix, $cfg_cmsurl;
	//属性处理
	$attlist="gid|0,orderby|dateline,orderway|desc,row|12,titlelen|30";
	FillAttsDefault($ctag->CAttribute->Items,$attlist);
	extract($ctag->CAttribute->Items, EXTR_SKIP);
	
	if( !$dsql->IsTable("{$cfg_dbprefix}groups") ) return '没安装圈子模块';

	if(!ereg("/$", $cfg_cmsurl)) $cfg_group_url = $cfg_cmsurl."/group";
	else $cfg_group_url = $cfg_cmsurl."group";
	
	$innertext = $ctag->GetInnerText();
	if(trim($innertext)=='') $innertext = GetSysTemplets('groupthreads.htm');
	
	$WhereSql = " WHERE t.closed=0 ";
	$orderby = 't.'.$orderby;
	if($gid > 0) $WhereSql .= " AND t.gid='$gid' ";
	
	$query = "SELECT t.subject,t.gid,t.tid,t.lastpost,g.groupname FROM `#@__group_threads` t 
	         left join `#@__groups` g on g.groupid=t.gid
	         $WhereSql ORDER BY $orderby $orderway LIMIT 0,{$row}";
	
	$dsql->SetQuery($query);
  $dsql->Execute();
  $ctp = new DedeTagParse();
  $ctp->SetNameSpace('field', '[', ']');
	if(!isset($list)) $list = '';
	while($rs = $dsql->GetArray())
	{
  	  $ctp->LoadSource($innertext);
  	  $rs['subject'] = cn_substr($rs['subject'], $titlelen);
  	  $rs['url'] = $cfg_group_url."/viewthread.php?id={$rs['gid']}&tid={$rs['tid']}";
  	  $rs['groupurl'] = $cfg_group_url."/group.php?id={$rs['gid']}";
  	  foreach($ctp->CTags as $tagid=>$ctag) {
		    if(!empty($rs[strtolower($ctag->GetName())])) { $ctp->Assign($tagid, $rs[$ctag->GetName()]); }
		  }
		  $list .= $ctp->GetResult();
	}
	return $list;
}

?>