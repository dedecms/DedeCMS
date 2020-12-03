<?php

//把单个文档的Tag写入索引
function InsertTags(&$dsql,$tagname,$aid,$mid=0,$typeid=0,$arcrank=0)
{
	$tagname = trim(ereg_replace("[,;'%><\"\*\?\r\n\t ]{1,}",',',$tagname));
	if(strlen($tagname)<2) return false;
	$ntime = time();
	$tags = explode(',',$tagname);
	$hasTags = array();
	$hasTagsM = array();
	$likequery = '';
	
	foreach($tags as $k){ $likequery .= ($likequery=='' ? "tagname like '$k'" : " or tagname like '$k'" ); }
	
	//获取已经存在的tag的id
	$dsql->Execute('0',"Select id,tagname From #@__tag_index where $likequery ");
	while($row = $dsql->GetArray('0',MYSQL_ASSOC)) $hasTags[strtolower($row['tagname'])] = $row['id'];
	
	//遍历tag，并依情况是否增加tag，并获得每个tag的索引id
	$tids = array();
	foreach($tags as $k)
	{
		$lk = strtolower($k);
		if(isset($hasTags[$lk])){
			$tid = $hasTags[$lk];
		}else{
			$dsql->ExecuteNoneQuery("INSERT INTO `#@__tag_index`(`tagname` , `count` , `result` , `weekcc` , `monthcc` , `addtime` ) VALUES('$k', '0', '0', '0', '0', '$ntime');");
			$tid = $dsql->GetLastID();
		}
		//if($mid>0 && !isset($hasTagsM[$lk])) $dsql->ExecuteNoneQuery("INSERT INTO `#@__tags_user`(`mid`,`tid`,`tagname`) VALUES('$mid','$tid', '$k');");
	  $tids[] = $tid;
	}
	
	//检查tag_list是否存在这些Tag，如果不存在则写入
	$tidstr = '';
	foreach($tids as $tid){
		$tidstr .= ($tidstr=='' ? $tid : ",{$tid}");
	}
	$hastids = array();
	if($tidstr!='')
	{
		$dsql->Execute('0',"Select tid,aid From #@__tag_list where tid in($tidstr) ");
		while($row = $dsql->GetArray('0',MYSQL_ASSOC)){
			$hastids[$row['tid']][] = $row['aid'];
		}
	}
	
	foreach($tids as $t)
	{
		if(!isset($hastids[$t])){
		  $dsql->ExecuteNoneQuery("INSERT INTO `#@__tag_list`(`tid`,`aid`,`typeid`,`arcrank`) VALUES('$t','$aid','$typeid','$arcrank');");
		}else
		{
			if(!in_array($aid,$hastids[$t])){
				$dsql->ExecuteNoneQuery("INSERT INTO `#@__tag_list`(`tid`,`aid`,`typeid`,`arcrank`) VALUES('$t','$aid','$typeid','$arcrank');");
			}
		}
	}
	
	return true;
}

//从索引中获得某文档的所有Tag
function GetTagFormLists(&$dsql,$aid)
{
	$tags = '';
	$dsql->Execute('t',"Select i.tagname From #@__tag_list t left join #@__tag_index i on i.id=t.tid where t.aid='$aid' ");
	while($row = $dsql->GetArray('t',MYSQL_ASSOC)){
	   $tags .= ($tags=='' ? "{$row['tagname']}" : ",{$row['tagname']}");
	}
	return $tags;
}

//移除不用的Tag
function UpTags(&$dsql,$tagname,$aid,$mid=0,$typeid=0)
{
	$dsql->ExecuteNoneQuery("Delete From `#@__tag_list` where aid='$aid' limit 100");
	InsertTags($dsql,$tagname,$aid,$mid,$typeid);
}

?>