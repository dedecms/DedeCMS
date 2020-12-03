<?php
//function GetTags($num,$ltype='new',$InnerText='')

function lib_tag(&$ctag,&$refObj)
{
	global $dsql,$envs,$cfg_cmsurl;
	//属性处理
	$attlist="row|30,sort|new,getall|0,typeid|0";
	FillAttsDefault($ctag->CAttribute->Items,$attlist);
	extract($ctag->CAttribute->Items, EXTR_SKIP);

	$InnerText = $ctag->GetInnerText();
	if(trim($InnerText)=='') $InnerText = GetSysTemplets('tag_one.htm');
	$revalue = '';

	$ltype = $sort;
	$num = $row;

	$addsql = '';

	if($getall==0 && isset($refObj->Fields['tags']) && !empty($refObj->Fields['aid']))
	{
		$dsql->SetQuery("Select tid From `#@__taglist` where aid = '{$refObj->Fields['aid']}' ");
		$dsql->Execute();
		$ids = '';
		while($row = $dsql->GetArray())
	  {
	  	$ids .= ( $ids=='' ? $row['tid'] : ','.$row['tid'] );
		}
		if($ids != '')
		{
			$addsql = " where id in($ids) ";
		}
		if($addsql=='') return '';
	}
	else
	{
		if(!empty($typeid))
		{
			$addsql = " where typeid='$typeid' ";
		}
  }
  
	if($ltype=='rand') $orderby = 'rand() ';
	else if($ltype=='week') $orderby=' weekcc desc ';
	else if($ltype=='month') $orderby=' monthcc desc ';
	else if($ltype=='hot') $orderby=' count desc ';
	else if($ltype=='total') $orderby=' total desc ';
	else $orderby = 'addtime desc  ';

	$dsql->SetQuery("Select * From `#@__tagindex` $addsql order by $orderby limit 0,$num");
	$dsql->Execute();

	$ctp = new DedeTagParse();
	$ctp->SetNameSpace('field','[',']');
	$ctp->LoadSource($InnerText);
	while($row = $dsql->GetArray())
	{
		$row['keyword'] = $row['tag'];
		$row['tag'] = htmlspecialchars($row['tag']);
		$row['link'] = $cfg_cmsurl."/tags.php?/".urlencode($row['keyword'])."/";
		$row['highlight'] = 0;
		if($row['monthcc']>1000 || $row['weekcc']>300 )
		{
			$row['highlight'] = mt_rand(3,4);
		}
		else if($row['count']>3000)
		{
			$row['highlight'] = mt_rand(5,6);
		}
		else
		{
			$row['highlight'] = mt_rand(1,2);
		}
		foreach($ctp->CTags as $tagid=>$ctag)
		{
			if(isset($row[$ctag->GetName()]))
			{
				$ctp->Assign($tagid,$row[$ctag->GetName()]);
			}
		}
		$revalue .= $ctp->GetResult();
	}
	return $revalue;
}
?>