<?php
require_once(dirname(__FILE__)."/config.php");
require_once(DEDEINC."/arc.caicai.class.php");
$sort = trim(empty($sort) ? 'lastpost' : eregi_replace('[^0-9a-z]','',$sort));
if(!eregi("^(scores|badpost|goodpost)$",$sort))
{
	$sort = 'lastpost';
}
$tid = (isset($tid) ? intval($tid) : 0);
$t1 = ExecTime();
$typequery = '';
$menutype = 'mydede';
$menutype_son = 'cc';
//获取栏目的子类、交叉分类
if($tid!=0)
{
	$arr = $dsql->GetOne("Select * From `#@__arctype` where id='$tid' And corank=0 ");
	if($cfg_list_son=='Y')
	{
		$CrossID = GetSonIds($tid,$arr['channeltype']);
	}
	else
	{
		$CrossID = $tid;
	}
	if($arr['cross']>0)
	{
		$selquery = '';
		if($arr['cross']==1)
		{
			$selquery = "Select id,topid From `#@__arctype` where typename like '{$arr['typename']}' And id<>'{$tid}' And topid<>'{$tid}'  ";
		}
		else
		{
			$arr['crossid'] = ereg_replace('[^0-9,]','',trim($arr['crossid']));
			if($arr['crossid']!='')
			{
				$selquery = "Select id,topid From `#@__arctype` where id in('{$arr['crossid']}') And id<>'{$tid}' And topid<>'{$tid}'  ";
			}
		}
		if($selquery!='')
		{
			$dsql->SetQuery($selquery);
			$dsql->Execute();
			while($arr = $dsql->GetArray())
			{
				$CrossID .= ($CrossID=='' ? $arr['id'] : ','.$arr['id']);
			}
		}
	}
	$typequery = " arc.typeid in($CrossID) And ";
}
$dlist = new Caicai();
$dlist->pageSize = 15;
$dlist->maxPageSize = 100;
$maxrc = $dlist->pageSize * $dlist->maxPageSize;
$query = "Select arc.*,m.userid,m.face,
          tp.typedir,tp.typename,tp.isdefault,tp.defaultname,tp.namerule,tp.namerule2,tp.ispart,tp.moresite,tp.siteurl,tp.sitepath
          From `#@__archives` arc left join `#@__arctype` tp on tp.id=arc.typeid left join `#@__member`  m on m.mid=arc.mid
          where $typequery arc.arcrank>-1
          order by arc.`{$sort}` desc limit $maxrc ";
$dlist->SetParameter('tid',$tid);
$dlist->SetParameter('sort',$sort);
$dlist->SetTemplate(DEDEMEMBER.'/templets/caicai.htm');
$dlist->SetSource($query);
$dlist->Display();
//echo ExecTime() - $t1;
?>