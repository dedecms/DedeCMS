<?php

if(!defined('DEDEINC')) exit('Request Error!');

function lib_arclist(&$ctag,&$refObj)
{
	global $envs;

	$autopartid = 0;
	$tagname = $ctag->GetTagName();
	$channelid = $ctag->GetAtt('channelid');

	if($tagname=='imglist' || $tagname=='imginfolist') {
		$listtype = 'image';
	}
	else if($tagname=='specart') {
		$channelid = -1;
		$listtype='';
	}
	else if($tagname=='coolart') {
		$listtype = 'commend';
	}
	else if($tagname=='autolist') {
		$autopartid = $ctag->GetAtt('partsort');
	}
	else {
		$listtype = $ctag->GetAtt('type');
	}

	//排序
	if($ctag->GetAtt('sort')!='') $orderby = $ctag->GetAtt('sort');
	else if($tagname=='hotart') $orderby = 'click';
	else $orderby = $ctag->GetAtt('orderby');

	//对相应的标记使用不同的默认innertext
	if(trim($ctag->GetInnerText()) != '') $innertext = $ctag->GetInnerText();
	else if($tagname=='imglist') $innertext = GetSysTemplets('part_imglist.htm');
	else if($tagname=='imginfolist') $innertext = GetSysTemplets('part_imginfolist.htm');
	else $innertext = GetSysTemplets("part_arclist.htm");

	//兼容titlelength
	if($ctag->GetAtt('titlelength')!='') $titlelen = $ctag->GetAtt('titlelength');
	else $titlelen = $ctag->GetAtt('titlelen');

	//兼容infolength
	if($ctag->GetAtt('infolength')!='') $infolen = $ctag->GetAtt('infolength');
	else $infolen = $ctag->GetAtt('infolen');

	$typeid = trim($ctag->GetAtt('typeid'));
	if(empty($typeid)) {
		$typeid = ( isset($refObj->Fields['typeid']) ? $refObj->Fields['typeid'] : $envs['typeid'] );
	}

	if($listtype=='autolist') {
		$typeid = lib_GetAutoChannelID($ctag->GetAtt('partsort'),$typeid);
	}

	if($ctag->GetAtt('att')=='') {
		$flag = $ctag->GetAtt('flag');
	}
	else {
		$flag = $ctag->GetAtt('att');
	}

	return lib_arclistDone
	       (
	         $refObj, $ctag, $typeid, $ctag->GetAtt('row'), $ctag->GetAtt('col'), $titlelen, $infolen,
	         $ctag->GetAtt('imgwidth'), $ctag->GetAtt('imgheight'), $listtype, $orderby,
	         $ctag->GetAtt('keyword'), $innertext, $envs['aid'], $ctag->GetAtt('idlist'), $channelid,
	         $ctag->GetAtt('limit'), $flag,$ctag->GetAtt('orderway'), $ctag->GetAtt('subday'), $ctag->GetAtt('noflag')
	       );
}

function lib_arclistDone(&$refObj, &$ctag, $typeid=0, $row=10, $col=1, $titlelen=30, $infolen=160,
        $imgwidth=120, $imgheight=90, $listtype='all', $orderby='default', $keyword='',
        $innertext='', $arcid=0, $idlist='', $channelid=0, $limit='', $att='', $order='desc', $subday=0, $noflag='')
{
	global $dsql,$PubFields,$cfg_keyword_like,$cfg_index_cache,$_arclistEnv,$envs,$cfg_cache_type;
	$row = AttDef($row,10);
	$titlelen = AttDef($titlelen,30);
	$infolen = AttDef($infolen,160);
	$imgwidth = AttDef($imgwidth,120);
	$imgheight = AttDef($imgheight,120);
	$listtype = AttDef($listtype,'all');
	$arcid = AttDef($arcid,0);
	$channelid = AttDef($channelid,0);
	$orderby = AttDef($orderby,'default');
	$orderWay = AttDef($order,'desc');
	$subday = AttDef($subday,0);
	$line = $row;
	$orderby=strtolower($orderby);
	$keyword = trim($keyword);
	$innertext = trim($innertext);

	$tablewidth=$ctag->GetAtt("tablewidth");
	if($tablewidth=="") $tablewidth = 100;
	if(empty($col)) $col = 1;
	$colWidth = ceil(100/$col);
	$tablewidth = $tablewidth."%";
	$colWidth = $colWidth."%";

	if($innertext=='') $innertext = GetSysTemplets('part_arclist.htm');
	if( @$ctag->GetAtt('getall') == 1 ) $getall = 1;
	else $getall = 0;

	if($att=='0') $att='';
	if($att=='3') $att='f';
	if($att=='1') $att='h';

	$orwheres = array();
	$maintable = '#@__archives';
	//按不同情况设定SQL条件 排序方式
	if($idlist=='')
	{
		if($orderby=='near' && $cfg_keyword_like=='N') { $keyword=''; }

		//时间限制(用于调用最近热门文章、热门评论之类)，这里的时间只能计算到天，否则缓存功能将无效
		if($subday > 0)
		{
			$ntime = gmmktime(0, 0, 0, gmdate('m'), gmdate('d'), gmdate('Y'));
			$limitday = $ntime - ($subday * 24 * 3600);
			$orwheres[] = " arc.senddate > $limitday ";
		}
		//关键字条件
		if($keyword!='')
		{
			$keyword = str_replace(',', '|', $keyword);
			$orwheres[] = " CONCAT(arc.title,arc.keywords) REGEXP '$keyword' ";
		}
		//文档属性
		if(eregi('commend',$listtype)) $orwheres[] = " FIND_IN_SET('c', arc.flag)>0  ";
		if(eregi('image',$listtype)) $orwheres[] = " FIND_IN_SET('p', arc.flag)>0  ";
		if($att != '') {
			$flags = explode(',', $att);
			for($i=0; isset($flags[$i]); $i++) $orwheres[] = " FIND_IN_SET('{$flags[$i]}', arc.flag)>0 ";
		}

		if(!empty($typeid))
		{
			//指定了多个栏目时，不再获取子类的id
			if( ereg(',', $typeid) )
			{
				//指定了getall属性或主页模板例外
				if($getall==1 || empty($refObj->Fields['typeid']))
				{
					$typeids = explode(',', $typeid);
					foreach($typeids as $ttid) {
						$typeidss[] = GetSonIds($ttid);
					}
					$typeidStr = join(',', $typeidss);
					$typeidss = explode(',', $typeidStr);
					$typeidssok = array_unique($typeidss);
					$typeid = join(',', $typeidssok);
				}
				$orwheres[] = " arc.typeid in ($typeid) ";
			}
			else
			{
				//处理交叉栏目
				$CrossID = '';
				if($ctag->GetAtt('cross')=='1')
				{
					$arr = $dsql->GetOne("Select `id`,`topid`,`cross`,`crossid`,`ispart`,`typename` From `#@__arctype` where id='$typeid' ");
					if( $arr['cross']==0 || ( $arr['cross']==2 && trim($arr['crossid']=='') ) )
					{
						$orwheres[] = ' arc.typeid in ('.GetSonIds($typeid).')';
				  }
					else
					{
						$selquery = '';
						if($arr['cross']==1) {
							$selquery = "Select id,topid From `#@__arctype` where typename like '{$arr['typename']}' And id<>'{$typeid}' And topid<>'{$typeid}'  ";
						}
						else {
							$arr['crossid'] = ereg_replace('[^0-9,]','',trim($arr['crossid']));
							if($arr['crossid']!='') $selquery = "Select id,topid From `#@__arctype` where id in('{$arr['crossid']}') And id<>'{$typeid}' And topid<>'{$typeid}'  ";
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
				}
				if($CrossID=='') $orwheres[] = ' arc.typeid in ('.GetSonIds($typeid).')';
				else $orwheres[] = ' arc.typeid in ('.GetSonIds($typeid).','.$CrossID.')';
			}
		}

		//频道ID
		if(eregi('spec', $listtype)) $channelid==-1;

		if(!empty($channelid)) $orwheres[] = " And arc.channel = '$channelid' ";

		if(!empty($noflag)) $orwheres[] = " FIND_IN_SET('$noflag', arc.flag)<1 ";

		$orwheres[] = ' arc.arcrank > -1 ';

		//由于这个条件会导致缓存功能失去意义，因此取消
		//if($arcid!=0) $orwheres[] = " arc.id<>'$arcid' ";
	}

	//文档排序的方式
	$ordersql = '';
	if($orderby=='hot' || $orderby=='click') $ordersql = " order by arc.click $orderWay";
	else if($orderby == 'sortrank' || $orderby=='pubdate') $ordersql = " order by arc.sortrank $orderWay";
	else if($orderby == 'id') $ordersql = "  order by arc.id $orderWay";
	else if($orderby == 'near') $ordersql = " order by ABS(arc.id - ".$arcid.")";
	else if($orderby == 'lastpost') $ordersql = "  order by arc.lastpost $orderWay";
	else if($orderby == 'scores') $ordersql = "  order by arc.scores $orderWay";
	else if($orderby == 'rand') $ordersql = "  order by rand()";
	else $ordersql = " order by arc.sortrank $orderWay";

	//limit条件
	$limit = trim(eregi_replace('limit','',$limit));
	if($limit!='') $limitsql = " limit $limit ";
	else $limitsql = " limit 0,$line ";

	$orwhere = '';
	if(isset($orwheres[0])) {
		$orwhere = join(' And ',$orwheres);
		$orwhere = ereg_replace("^ And",'',$orwhere);
		$orwhere = ereg_replace("And[ ]{1,}And",'And ',$orwhere);
	}
	if($orwhere!='') $orwhere = " where $orwhere ";
	
	//获取附加表信息
	$addfield = trim($ctag->GetAtt('addfields'));
	$addfieldsSql = '';
	$addfieldsSqlJoin = '';
	if($addfield != '' && !empty($channelid))
	{
		$row = $dsql->GetOne("Select addtable From `#@__channeltype` where id='$channelid' ");
		if(isset($row['addtable']) && trim($row['addtable']) != '')
		{
			$addtable = trim($row['addtable']);
			$addfields = explode(',', $addfield);
			$row['addtable'] = trim($row['addtable']);
			$addfieldsSql = ",addf.".join(',addf.', $addfields);
			$addfieldsSqlJoin = " left join `$addtable` addf on addf.aid = arc.id ";
		}
	}

	$query = "Select arc.*,tp.typedir,tp.typename,tp.corank,tp.isdefault,tp.defaultname,tp.namerule,
		tp.namerule2,tp.ispart,tp.moresite,tp.siteurl,tp.sitepath
		$addfieldsSql
		from `$maintable` arc left join `#@__arctype` tp on arc.typeid=tp.id
		$addfieldsSqlJoin
		$orwhere $ordersql $limitsql";

	$md5hash = md5($query);
	$stylehash = ($cfg_cache_type=='content' ? md5($innertext) : '');
	$needSaveCache = true;
	
	if($idlist!='' || $GLOBALS['_arclistEnv']=='index' || $cfg_index_cache==0)
	{
		$needSaveCache = false;
	}
	else
	{
		$idlist = GetArclistCache($md5hash, $stylehash);
		if($idlist != '') {
			$needSaveCache = false;
		}
		//如果使用的是内容缓存，直接返回结果
		if($cfg_cache_type=='content' && $idlist != '')
		{
			$idlist = ($idlist==0 ? '' : $idlist);
			return $idlist;
		}
	}

	//指定了id或使用缓存中的id
	if($idlist != '')
	{
		$query = "Select arc.*,tp.typedir,tp.typename,tp.corank,tp.isdefault,tp.defaultname,tp.namerule,tp.namerule2,tp.ispart,
			tp.moresite,tp.siteurl,tp.sitepath
			$addfieldsSql
			 from `$maintable` arc left join `#@__arctype` tp on arc.typeid=tp.id
			 $addfieldsSqlJoin
		  where arc.id in($idlist) $ordersql ";
	}

	$dsql->SetQuery($query);
	$dsql->Execute('al');
  $artlist = '';
	if($col>1) $artlist = "<table width='$tablewidth' border='0' cellspacing='0' cellpadding='0'>\r\n";
	$dtp2 = new DedeTagParse();
	$dtp2->SetNameSpace('field', '[', ']');
	$dtp2->LoadString($innertext);
	$GLOBALS['autoindex'] = 0;
	$ids = array();
	for($i=0; $i<$line; $i++)
	{
		if($col>1) $artlist .= "<tr>\r\n";
		for($j=0; $j<$col; $j++)
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
				//$row['title'] = "<b>".$row['title']."</b>";

				$row['textlink'] = "<a href='".$row['filename']."'>".$row['title']."</a>";

				$row['plusurl'] = $row['phpurl'] = $GLOBALS['cfg_phpurl'];
				$row['memberurl'] = $GLOBALS['cfg_memberurl'];
				$row['templeturl'] = $GLOBALS['cfg_templeturl'];

				if(is_array($dtp2->CTags))
				{
					foreach($dtp2->CTags as $k=>$ctag)
					{
						if($ctag->GetName()=='array') {
							//传递整个数组，在runphp模式中有特殊作用
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
			}//if hasRow
			else{
				$artlist .= '';
			}
			if($col>1) $artlist .= "	</td>\r\n";
		}//Loop Col
		if($col>1) $i += $col - 1;
		if($col>1) $artlist .= "	</tr>\r\n";
	}//loop line
	if($col>1) $artlist .= "	</table>\r\n";
	$dsql->FreeResult("al");
	//保存ID缓存
	$idsstr = join(',',$ids);
	if($needSaveCache)
	{
		if($idsstr=='') $idsstr = '0';
		if($cfg_cache_type=='content' && $idsstr!='0') {
			$idsstr = $artlist;
		}
		$inquery = "INSERT INTO `#@__arccache`(`md5hash`,`stylehash`,`uptime`,`cachedata`) VALUES ('".$md5hash."','$stylehash', '".time()."', '$idsstr'); ";
		$dsql->ExecuteNoneQuery("Delete From `#@__arccache` where md5hash='".$md5hash."' ");
		$dsql->ExecuteNoneQuery($inquery);
	}
	return $artlist;
}

//查询缓存
function GetArclistCache($md5hash, $stylehash)
{
	global $dsql,$envs,$cfg_makesign_cache,$cfg_index_cache,$cfg_cache_type;
	if($cfg_index_cache <= 0) return '';
	if(isset($envs['makesign']) && $cfg_makesign_cache=='N') return '';
	$mintime = time() - $cfg_index_cache;
	if($cfg_cache_type=='id') {
		$arr = $dsql->GetOne("Select cachedata,uptime From `#@__arccache` where md5hash = '$md5hash' ");
	}
	else {
		$arr = $dsql->GetOne("Select cachedata,uptime From `#@__arccache` where md5hash = '$md5hash' And stylehash='$stylehash' ");
	}
	if(!is_array($arr)) {
		return '';
	}
	else if($arr['uptime'] < $mintime) {
		return '';
	}
	else {
		return $arr['cachedata'];
	}
}

function lib_GetAutoChannelID($sortid,$topid)
{
	global $dsql;
	if(empty($sortid)) $sortid = 1;
	$getstart = $sortid - 1;
	$row = $dsql->GetOne("Select id,typename From #@__arctype where reid='{$topid}' And ispart<2 And ishidden<>'1' order by sortrank asc limit $getstart,1");
	if(!is_array($row)) return 0;
	else return $row['id'];
}
?>