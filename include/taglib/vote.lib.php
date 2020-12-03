<?php
if(!defined('DEDEINC'))
{
	exit("Request Error!");
}
require_once(DEDEINC.'/dedevote.class.php');
function lib_vote(&$ctag,&$refObj)
{
	global $dsql;
	$attlist="id|0,lineheight|24,tablewidth|100%,titlebgcolor|#EDEDE2,titlebackgroup|,tablebg|#FFFFFF";
	FillAttsDefault($ctag->CAttribute->Items,$attlist);
	extract($ctag->CAttribute->Items, EXTR_SKIP);

	if(empty($id)) $id=0;
	if($id==0)
	{
		$row = $dsql->GetOne("select aid From `#@__vote` order by aid desc limit 0,1");
		if(!isset($row['aid'])) return '';
		else $id=$row['aid'];
	}
	$vt = new DedeVote($id);
	return $vt->GetVoteForm($lineheight,$tablewidth,$titlebgcolor,$titlebackgroup,$tablebg);
}
?>