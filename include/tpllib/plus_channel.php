<?php
if(!defined('DEDEINC')) exit('Request Error!');
require_once(DEDEINC.'/channelunit.func.php');

function plus_channel(&$atts, &$refObj, &$fields)
{
	global $dsql,$_vars;

	$attlist = "typeid=0,reid=0,row=100,type=son,currentstyle=";
  FillAtts($atts,$attlist);
  FillFields($atts,$fields,$refObj);
	extract($atts, EXTR_OVERWRITE);

	$line = empty($row) ? 100 : $row;
	$reArray = array();
	$reid = 0;
	$topid = 0;
	
	//如果属性里没指定栏目id，从引用类里获取栏目信息
	if(empty($typeid))
	{
		if( isset($refObj->TypeLink->TypeInfos['id']) )
		{
			$typeid = $refObj->TypeLink->TypeInfos['id'];
			$reid = $refObj->TypeLink->TypeInfos['reid'];
			$topid = $refObj->TypeLink->TypeInfos['topid'];
		}
		else {
	  	$typeid = 0;
	  }
	}
	//如果指定了栏目id，从数据库获取栏目信息
	else
	{
		$row2 = $dsql->GetOne("Select * From `#@__arctype` where id='$typeid' ");
		$typeid = $row2['id'];
		$reid = $row2['reid'];
		$topid = $row2['topid'];
		$issetInfos = true;
	}
	
	if($type=='' || $type=='sun') $type='son';

	if($type=='top')
	{
		$sql = "Select id,typename,typedir,isdefault,ispart,defaultname,namerule2,moresite,siteurl,sitepath
		  From `#@__arctype` where reid=0 And ishidden<>1 order by sortrank asc limit 0, $line ";
	}
	else if($type=='son')
	{
		if($typeid==0) return $reArray;
		$sql = "Select id,typename,typedir,isdefault,ispart,defaultname,namerule2,moresite,siteurl,sitepath
		  From `#@__arctype` where reid='$typeid' And ishidden<>1 order by sortrank asc limit 0, $line ";
	}
	else if($type=='self')
	{
		if($reid==0) return $reArray;
		$sql = "Select id,typename,typedir,isdefault,ispart,defaultname,namerule2,moresite,siteurl,sitepath
			From `#@__arctype` where reid='$reid' And ishidden<>1 order by sortrank asc limit 0, $line ";
	}

	//检查是否有子栏目，并返回rel提示（用于二级菜单）
	$needRel = true;
	
	if(empty($sql)) return $reArray;

	$dsql->Execute('me',$sql);
	$totalRow = $dsql->GetTotalRow('me');
	
	//如果用子栏目模式，当没有子栏目时显示同级栏目
	if($type=='son' && $reid!=0 && $totalRow==0)
	{
		$sql = "Select id,typename,typedir,isdefault,ispart,defaultname,namerule2,moresite,siteurl,sitepath
			From `#@__arctype` where reid='$reid' And ishidden<>1 order by sortrank asc limit 0, $line ";
	  $dsql->Execute('me', $sql);
	}
	$GLOBALS['autoindex'] = 0;
	while($row=$dsql->GetArray())
	{
				$row['currentstyle'] = $row['sonids'] = $row['rel'] = '';
				if($needRel)
				{
					$row['sonids'] = GetSonIds($row['id'], 0, false);
					if($row['sonids']=='') $row['rel'] = '';
					else $row['rel'] = " rel='dropmenu{$row['id']}'";
				}
				//处理同级栏目中，当前栏目的样式
				if( ($row['id']==$typeid || ($topid==$row['id'] && $type=='top') ) && $currentstyle!='' )
				{
					$row['currentstyle'] = $currentstyle;
				}
				$row['typelink'] = $row['typeurl'] = GetOneTypeUrlA($row);
				$reArray[] = $row;
			  $GLOBALS['autoindex']++;
	}
	//Loop for $i
	$dsql->FreeResult();
	return $reArray;
}
?>