<?php
function lib_arclistsg(&$ctag,&$refObj)
{
	global $dsql,$PubFields,$cfg_keyword_like,$cfg_index_cache,$_arclistEnv,$envs,$_sys_globals;

	//属性处理
	$attlist="typeid|0,row|10,col|1,flag|,titlelen|30,sort|default,keyword|,innertext|,arcid|0,idlist|,channelid|0,limit|,orderway|desc,subday|0";
	FillAttsDefault($ctag->CAttribute->Items,$attlist);
	extract($ctag->CAttribute->Items, EXTR_SKIP);

	$line = $row;
	$orderby=strtolower($sort);
	if($col=='') $col = 1;
	$innertext = trim($ctag->GetInnerText());
	if($innertext=='') $innertext = GetSysTemplets("part_arclistsg.htm");

	if(empty($channelid) && isset($GLOBALS['envs']['channelid'])) {
		$channelid = $GLOBALS['envs']['channelid'];
	}
	
	if(empty($typeid) && !empty($envs['typeid'])) {
  	$typeid = $envs['typeid'];
	}
	
	if(empty($typeid) && empty($channelid))
	{
		return "No channel info!";
  }

  if(!empty($channelid)) $gquery = "Select addtable,listfields From `#@__channeltype` where id='$channelid' ";
  else $gquery = "Select ch.addtable,listfields From `#@__arctype` tp left join `#@__channeltype` ch on ch.id=tp.channeltype where id='$typeid'";

  $row = $dsql->GetOne($gquery);

	$orwheres = array();
	$maintable = trim($row['addtable']);

	if($maintable=='')
	{
		return "No addtable info!";
	}

	//列表调用字段
	$listarcs = array('aid','typeid');
	if(!empty($row['listfields']))
	{
		 $listfields = explode(',',$row['listfields']);
		 foreach($listfields as $v)
		 {
			  if(!in_array($v,$listarcs)) $listarcs[] = $v;
		 }
	}
	$arclistquery = join(',',$listarcs);
	$arclistquery .= ",arc.aid as id,arc.senddate as pubdate";

	//按不同情况设定SQL条件 排序方式
	if($idlist=='')
	{
		if($orderby=='near' && $cfg_keyword_like=='N'){ $keyword=''; }
		//时间限制(用于调用最近热门文章、热门评论之类)
		if($subday>0)
		{
			//这里的时间只能计算到天，否则缓存功能将无效
			$ntime = gmmktime(0, 0, 0, gmdate('m'), gmdate('d'), gmdate('Y'));
			$limitday = $ntime - ($subday * 24 * 3600);
			$orwheres[] = " arc.senddate > $limitday ";
		}
		
		if($flag!='')
		{
			$flags = explode(',',$flag);
			for($i=0;isset($flags[$i]);$i++) $orwheres[] = " FIND_IN_SET('{$flags[$i]}',flag)>0 ";
		}

		if(!empty($typeid))
		{
			//指定了多个栏目时，不再获取子类的id
			if(ereg(',',$typeid)) $orwheres[] = " typeid in ($typeid) ";
			else
			{
				//处理交叉栏目
				$CrossID = '';
				if((isset($envs['cross']) || $ctag->GetAtt('cross')=='1' ) && $ctag->GetAtt('nocross')!='1')
				{
					$arr = $dsql->GetOne("Select `id`,`topid`,`cross`,`crossid`,`ispart`,`typename` From `#@__arctype` where id='$typeid' ");
					if($arr['cross']==0 || ($arr['cross']==2 && trim($arr['crossid']=='')))
					$orwheres[] = ' typeid in ('.GetSonIds($typeid).')';
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
							while($arr = $dsql->GetArray()) {
								$CrossID .= ($CrossID=='' ? $arr['id'] : ','.$arr['id']);
							}
						}
					}
				}
				if($CrossID=='') $orwheres[] = ' typeid in ('.GetSonIds($typeid).')';
				else $orwheres[] = ' typeid in ('.GetSonIds($typeid).','.$CrossID.')';
			}
		}
		//频道ID

		if(!empty($channelid)) $orwheres[] = " And arc.channel = '$channelid' ";

		//由于这个条件会导致缓存功能失去意义，因此取消
		//if($arcid!=0) $orwheres[] = " arc.id<>'$arcid' ";
	}
	//文档排序的方式
	$ordersql = '';
	if($orderby=='hot'||$orderby=='click') $ordersql = " order by arc.click $orderway";
	else if($orderby=='id') $ordersql = "  order by arc.aid $orderway";
	else if($orderby=='near') $ordersql = " order by ABS(arc.id - ".$arcid.")";
	else if($orderby=='rand') $ordersql = "  order by rand()";
	else $ordersql=" order by arc.aid $orderway";
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

	$query = "Select $arclistquery,tp.typedir,tp.typename,tp.isdefault,tp.defaultname,tp.namerule,
		tp.namerule2,tp.ispart,tp.moresite,tp.siteurl,tp.sitepath
		from `$maintable` arc left join `#@__arctype` tp on arc.typeid=tp.id
		$orwhere and arc.arcrank > -1 $ordersql $limitsql";

	$md5hash = md5($query);
	$needcache = true;
	if($idlist!='') $needcache = false;
	else{
		$idlist = GetArclistSgCache($md5hash);
		if($idlist!='') $needcache = false;
	}
	//指定了id或使用缓存中的id
	if($idlist!='' && $_arclistEnv != 'index')
	{
		$query = "Select $arclistquery,tp.typedir,tp.typename,tp.isdefault,tp.defaultname,tp.namerule,tp.namerule2,tp.ispart,
			tp.moresite,tp.siteurl,tp.sitepath from `$maintable` arc left join `#@__arctype` tp on arc.typeid=tp.id
		  where arc.aid in($idlist) $ordersql $limitsql";
	}
	$dsql->SetQuery($query);
	$dsql->Execute("al");
	$artlist = "";
	$dtp2 = new DedeTagParse();
	$dtp2->SetNameSpace("field","[","]");
	$dtp2->LoadString($innertext);
	$GLOBALS['autoindex'] = 0;
	$ids = array();
	for($i=0;$i<$line;$i++)
	{
		for($j=0;$j<$col;$j++)
		{
			if($col>1) $artlist .= "	<div>\r\n";
			if($row = $dsql->GetArray("al"))
			{
				$ids[] = $row['aid'];

				$row['filename'] = $row['arcurl'] = GetFileUrl($row['id'],$row['typeid'],$row['senddate'],$row['title'],1,
				0,$row['namerule'],$row['typedir'],0,'',$row['moresite'],$row['siteurl'],$row['sitepath']);

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
				
				$row['image'] = "<img src='".$row['picname']."' border='0' alt='".ereg_replace("['><]","",$row['title'])."' />";
				$row['imglink'] = "<a href='".$row['filename']."'>".$row['image']."</a>";

				$row['stime'] = GetDateMK($row['pubdate']);
				$row['typelink'] = "<a href='".$row['typeurl']."'>".$row['typename']."</a>";
				$row['fulltitle'] = $row['title'];
				$row['title'] = cn_substr($row['title'],$titlelen);
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
						}else{
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
			if($col>1) $artlist .= "	</div>\r\n";
		}//Loop Col
		if($col>1) $i += $col - 1;
	}//loop line
	$dsql->FreeResult("al");
	//保存ID缓存
	$idsstr = join(',',$ids);
	if($idsstr!='' && $needcache && $cfg_index_cache>0)
	{
		$mintime = time() - ($cfg_index_cache * 3600);
		$inquery = "INSERT INTO `#@__arccache`(`md5hash`,`uptime`,`cachedata`) VALUES ('".$md5hash."', '".time()."', '$idsstr'); ";
		$dsql->ExecuteNoneQuery("Delete From `#@__arccache` where md5hash='".$md5hash."' or uptime < $mintime ");
		$dsql->ExecuteNoneQuery($inquery);
	}
	return $artlist;
}

//查询缓存
function GetArclistSgCache($md5hash)
{
	global $dsql,$envs,$cfg_makesign_cache,$cfg_index_cache;
	//没启用缓存
	if($cfg_index_cache<=0) return '';
	//少量更新禁用缓存
	if(isset($envs['makesign']) && $cfg_makesign_cache=='N') return '';
	//正常情况
	$mintime = time() - ($cfg_index_cache * 3600);
	$arr = $dsql->GetOne("Select cachedata,uptime From `#@__arccache` where md5hash = '$md5hash' and uptime > $mintime ");
	//没数据
	if(!is_array($arr)) return '';
	//返回缓存id数据
	else return $arr['cachedata'];
}

function lib_GetAutoChannelID2($sortid,$topid)
{
	global $dsql;
	if(empty($sortid)) $sortid = 1;
	$getstart = $sortid - 1;
	$row = $dsql->GetOne("Select id,typename From #@__arctype where reid='{$topid}' And ispart<2 And ishidden<>'1' order by sortrank asc limit $getstart,1");
	if(!is_array($row)) return 0;
	else return $row['id'];
}
?>