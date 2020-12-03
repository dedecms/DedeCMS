<?php

if(!defined('DEDEINC')) exit('Request Error!');

function lib_likearticle(&$ctag,&$refObj)
{
	global $dsql;
	
	//属性处理
	$attlist="row|12,titlelen|28,infolen|150,col|1,tablewidth|100,mytypeid|0,byabs|0,imgwidth|120,imgheight|90";
	FillAttsDefault($ctag->CAttribute->Items,$attlist);
	extract($ctag->CAttribute->Items, EXTR_SKIP);
	$revalue = '';
	
	if(empty($tablewidth)) $tablewidth = 100;
	if(empty($col)) $col = 1;
	$colWidth = ceil(100/$col);
	$tablewidth = $tablewidth."%";
	$colWidth = $colWidth."%";
	
	$ids = array();
	$tids = array();
	
	if(!empty($refObj->Fields['tags'])) {
		$keyword = $refObj->Fields['tags'];
	}
	else {
		$keyword = ( !empty($refObj->Fields['keywords']) ? $refObj->Fields['keywords'] : '' );
	}
	
	$typeid = ( !empty($mytypeid) ? $mytypeid : 0 );
	if(empty($typeid))
	{
			if(!empty($refObj->Typelink->TypeInfos['reid'])) {
				 $typeid = $refObj->Typelink->TypeInfos['reid'];
			}
			else {
				 if(!empty($refObj->Fields['typeid'])) $typeid = $refObj->Fields['typeid'];
			}
	}
	
	if( !empty($typeid) && !ereg(',', $typeid) ) {
			$typeid = GetSonIds($typeid);
	}
	
	$limitRow = $row - count($ids);
	$keyword = '';
	if(!empty($refObj->Fields['keywords']))
	{
			$keywords = explode(',' , trim($refObj->Fields['keywords']));
			$keyword = '';
			$n = 1;
			foreach($keywords as $k)
			{
				 	if($n > 3)  break;
				 	
				 	if(trim($k)=='') continue;
				 	else $k = addslashes($k);
				 	
				 	$keyword .= ($keyword=='' ? " CONCAT(arc.keywords,' ',arc.title) like '%$k%' " : " OR CONCAT(arc.keywords,' ',arc.title) like '%$k%' ");
			    $n++;
			}
	}
	$arcid = (!empty($refObj->Fields['id']) ? $refObj->Fields['aid'] : 0);
	if( empty($arcid) || $byabs==0 )	{
		$orderquery = " order by arc.id desc ";	 
	}
	else {
		$orderquery = " order by ABS(arc.id - ".$arcid.") ";
  }
	if($keyword != '')
	{
			 if(!empty($typeid)) {
			 		$typeid = " And arc.typeid in($typeid) And arc.id<>$arcid ";
			 }
			 $query = "Select arc.*,tp.typedir,tp.typename,tp.corank,tp.isdefault,tp.defaultname,tp.namerule,
		          tp.namerule2,tp.ispart,tp.moresite,tp.siteurl,tp.sitepath
		          from `#@__archives` arc left join `#@__arctype` tp on arc.typeid=tp.id
		          where arc.arcrank>-1 and ($keyword)  $typeid $orderquery limit 0, $row";
	}
	else
	{
			if(!empty($typeid)) {
					$typeid = " arc.typeid in($typeid) And arc.id<>$arcid ";
			}
			$query = "Select arc.*,tp.typedir,tp.typename,tp.corank,tp.isdefault,tp.defaultname,tp.namerule,
		          tp.namerule2,tp.ispart,tp.moresite,tp.siteurl,tp.sitepath
		          from `#@__archives` arc left join `#@__arctype` tp on arc.typeid=tp.id
		         where arc.arcrank>-1 and  $typeid $orderquery limit 0, $row";
	}
	
	$innertext = trim( $ctag->GetInnerText() );
	if($innertext=='') $innertext = GetSysTemplets('part_arclist.htm');

	$dsql->SetQuery($query);
	$dsql->Execute('al');
  $artlist = '';
	if($col > 1) {
		$artlist = "<table width='$tablewidth' border='0' cellspacing='0' cellpadding='0'>\r\n";
	}
	$dtp2 = new DedeTagParse();
	$dtp2->SetNameSpace('field', '[', ']');
	$dtp2->LoadString($innertext);
	$GLOBALS['autoindex'] = 0;
	$line = $row;
	for($i=0; $i < $line; $i++)
	{
		if($col>1) $artlist .= "<tr>\r\n";
		for($j=0; $j < $col; $j++)
		{
			if($col>1) $artlist .= "	<td width='$colWidth'>\r\n";
			if($row = $dsql->GetArray("al"))
			{
				$ids[] = $row['id'];
				//处理一些特殊字段
				$row['info'] = $row['infos'] = cn_substr($row['description'],$infolen);
				$row['id'] =  $row['id'];

				if($row['corank'] > 0 && $row['arcrank']==0)
				{
						$row['arcrank'] = $row['corank'];
				}

				$row['filename'] = $row['arcurl'] = GetFileUrl($row['id'],$row['typeid'],$row['senddate'],$row['title'],$row['ismake'],
				$row['arcrank'],$row['namerule'],$row['typedir'],$row['money'],$row['filename'],$row['moresite'],$row['siteurl'],$row['sitepath']);

				$row['typeurl'] = GetTypeUrl($row['typeid'],$row['typedir'],$row['isdefault'],$row['defaultname'],$row['ispart'],
				$row['namerule2'],$row['moresite'],$row['siteurl'],$row['sitepath']);

				if($row['litpic'] == '-' || $row['litpic'] == '')
				{
					$row['litpic'] = $GLOBALS['cfg_cmspath'].'/images/defaultpic.gif';
				}
				if(!eregi("^http://",$row['litpic']) && $GLOBALS['cfg_multi_site'] == 'Y')
				{
					$row['litpic'] = $GLOBALS['cfg_mainsite'].$row['litpic'];
				}
				$row['picname'] = $row['litpic'];
				$row['stime'] = GetDateMK($row['pubdate']);
				$row['typelink'] = "<a href='".$row['typeurl']."'>".$row['typename']."</a>";
				$row['image'] = "<img src='".$row['picname']."' border='0' width='$imgwidth' height='$imgheight' alt='".ereg_replace("['><]","",$row['title'])."'>";
				$row['imglink'] = "<a href='".$row['filename']."'>".$row['image']."</a>";
				$row['fulltitle'] = $row['title'];
				$row['title'] = cn_substr($row['title'],$titlelen);
				if($row['color']!='') $row['title'] = "<font color='".$row['color']."'>".$row['title']."</font>";
				if(ereg('b',$row['flag'])) $row['title'] = "<strong>".$row['title']."</strong>";
				$row['textlink'] = "<a href='".$row['filename']."'>".$row['title']."</a>";
				$row['plusurl'] = $row['phpurl'] = $GLOBALS['cfg_phpurl'];
				$row['memberurl'] = $GLOBALS['cfg_memberurl'];
				$row['templeturl'] = $GLOBALS['cfg_templeturl'];
				
				if(is_array($dtp2->CTags))
				{
					foreach($dtp2->CTags as $k=>$ctag)
					{
						if($ctag->GetName()=='array') {
							$dtp2->Assign($k,$row);
						}
						else {
							if(isset($row[$ctag->GetName()])) $dtp2->Assign($k,$row[$ctag->GetName()]);
							else $dtp2->Assign($k,'');
						}
					}
					$GLOBALS['autoindex']++;
				}

				$artlist .= $dtp2->GetResult()."\r\n";
			}
			//if hasRow
			else
			{
				$artlist .= '';
			}
			if($col>1) $artlist .= "	</td>\r\n";
		}
		//Loop Col
		if($col>1) $i += $col - 1;
		if($col>1) $artlist .= "	</tr>\r\n";
	}
	//loop line
	if($col>1) $artlist .= "	</table>\r\n";
	$dsql->FreeResult("al");
	return $artlist;
}
?>