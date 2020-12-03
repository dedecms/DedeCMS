<?php
function lib_channel(&$ctag,&$refObj)
{
	global $dsql;

	$attlist = "typeid|0,reid|0,row|100,col|1,type|son,currentstyle|";
	FillAttsDefault($ctag->CAttribute->Items,$attlist);
	extract($ctag->CAttribute->Items, EXTR_SKIP);
	$innertext = $ctag->GetInnerText();

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
	if($innertext=='') $innertext = GetSysTemplets("channel_list.htm");

	$likeType = '';
	if($type=='top')
	{
		$sql = "Select id,typename,typedir,isdefault,ispart,defaultname,namerule2,moresite,siteurl,sitepath
		  From `#@__arctype` where reid=0 And ishidden<>1 order by sortrank asc limit 0,$row";
	}
	else if($type=='son')
	{
		if($typeid==0) return '';
		$sql = "Select id,typename,typedir,isdefault,ispart,defaultname,namerule2,moresite,siteurl,sitepath
		  From `#@__arctype` where reid='$typeid' And ishidden<>1 order by sortrank asc limit 0,$row";
	}
	else if($type=='self')
	{
		if($reid==0) return '';
		$sql = "Select id,typename,typedir,isdefault,ispart,defaultname,namerule2,moresite,siteurl,sitepath
			From `#@__arctype` where reid='$reid' And ishidden<>1 order by sortrank asc limit 0,$row";
	}
	//And id<>'$typeid'
	$dtp2 = new DedeTagParse();
	$dtp2->SetNameSpace('field','[',']');
	$dtp2->LoadSource($innertext);
	
	$dsql->SetQuery($sql);
	$dsql->Execute();
	$line = $row;
	
	$totalRow = $dsql->GetTotalRow();
	//如果用子栏目模式，当没有子栏目时显示同级栏目
	if($type=='son' && $reid!=0 && $totalRow==0)
	{
		$sql = "Select id,typename,typedir,isdefault,ispart,defaultname,namerule2,moresite,siteurl,sitepath
			From `#@__arctype` where reid='$reid' And ishidden<>1 order by sortrank asc limit 0,$row";
		$dsql->SetQuery($sql);
	  $dsql->Execute();
	}
	
	$GLOBALS['autoindex'] = 0;
	for($i=0;$i < $line;$i++)
	{
		if($col>1) $likeType .= "<dl>\r\n";
		for($j=0; $j<$col; $j++)
		{
			if($col>1) $likeType .= "<dd>\r\n";
			if($row=$dsql->GetArray())
			{
				//处理同级栏目中，当前栏目的样式
				if( ($row['id']==$typeid || ($topid==$row['id'] && $type=='top') ) && $currentstyle!='' )
				{
					$linkOkstr = $currentstyle;
					$row['typelink'] = GetOneTypeUrlA($row);
					$linkOkstr = str_replace("~typelink~",$row['typelink'],$linkOkstr);
					$linkOkstr = str_replace("~typename~",$row['typename'],$linkOkstr);
					$likeType .= $linkOkstr;
				}
				else
				{
					$row['typelink'] = $row['typeurl'] = GetOneTypeUrlA($row);
					if(is_array($dtp2->CTags))
					{
						foreach($dtp2->CTags as $tagid=>$ctag)
						{
							if(isset($row[$ctag->GetName()])) $dtp2->Assign($tagid,$row[$ctag->GetName()]);
						}
					}
					$likeType .= $dtp2->GetResult();
				}
			}
			if($col>1) $likeType .= "</dd>\r\n";
			$GLOBALS['autoindex']++;
		}
		//Loop Col
		if($col>1)
		{
			$i += $col - 1;
			$likeType .= "	</dl>\r\n";
		}
	}
	//Loop for $i
	$dsql->FreeResult();
	return $likeType;
}
?>