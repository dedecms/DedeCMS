<?php
if(!defined('DEDEINC'))
{
	exit("Request Error!");
}

/**
 * 调用最新评论
 *
 * @param int row 12
 * int infolen 10
 * @param int titlelen 100
 * @return unknown
 */
function lib_feedback(&$ctag,&$refObj)
{
	global $dsql;
	$attlist="row|12,titlelen|24,infolen|100";
	FillAttsDefault($ctag->CAttribute->Items,$attlist);
	extract($ctag->CAttribute->Items, EXTR_SKIP);
	$innertext = trim($ctag->GetInnerText());
	$totalrow = $row;
	$revalue = '';
	if(empty($innertext))
	{
		$innertext = GetSysTemplets('tag_feedback.htm');
	}
	$wsql = " where ischeck=1 ";
	$equery = "SELECT * FROM `#@__feedback` $wsql ORDER BY id DESC LIMIT 0 , $totalrow";
	$ctp = new DedeTagParse();
	$ctp->SetNameSpace('field','[',']');
	$ctp->LoadSource($innertext);

	$dsql->Execute('fb',$equery);
	while($arr=$dsql->GetArray('fb'))
	{
		$arr['title'] = cn_substr($arr['arctitle'],$titlelen);
		$arr['msg'] = jstrim($arr['msg'],$infolen);
		foreach($ctp->CTags as $tagid=>$ctag)
		{
			if(!empty($arr[$ctag->GetName()]))
			{
				$ctp->Assign($tagid,$arr[$ctag->GetName()]);
			}
		}
		$revalue .= $ctp->GetResult();
	}
	return $revalue;
}
?>