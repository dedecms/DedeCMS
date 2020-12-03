<?php
if(!defined('DEDEINC'))
{
	exit("Request Error!");
}

function plus_newvisitor(&$atts,&$refObj,&$fields)
{
	global $dsql,$_vars,$cfg_memberurl;

	$attlist = "titlelen=30,infolen=200,row=6";
	FillAtts($atts,$attlist);
	FillFields($atts,$fields,$refObj);
	extract($atts, EXTR_OVERWRITE);
	$mid = $_vars['mid'];

	$query = "Select h.*,mb.face,mb.sex,mb.userid as loginid,mb.uname,s.sign From `#@__member_vhistory` h
	         left join `#@__member` mb on mb.mid = h.vid
	         left join `#@__member_space` s on s.mid = h.vid
	         where  h.mid='$mid' order by h.vtime desc limit 0,$row";

	$dsql->SetQuery($query);
	$dsql->Execute("al");
	$rearr = array();
	while($row = $dsql->GetArray("al"))
	{
		$row['url'] = $cfg_memberurl."/index.php?uid=".$row['loginid'];
		if(empty($row['face']))
		{
			$row['face']=($row['sex']=='Ů')? $cfg_memberurl.'/templets/images/dfgirl.png' : $cfg_memberurl.'/templets/images/dfboy.png';
		}
		$rearr[] = $row;
	}
	$dsql->FreeResult("al");
	return $rearr;
}

?>