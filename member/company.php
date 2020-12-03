<?php
if(empty($keyword)) $keyword = '';
if(empty($mtype)) $mtype = 0;
if(empty($type)){
	//获取企业新闻start
	$query = "Select arc.*,tp.typename
			From `#@__full_search` arc left join #@__arctype tp on tp.ID = arc.typeid
			where arc.mid='{$spaceInfos['ID']}' and arc.channelid=1 order by arc.aid desc limit 0,6;
		";
	$dsql->SetQuery($query);
	$dsql->Execute();
	$news = array();
	while($row = $dsql->GetArray()){
		$row['arcurl'] = $row['url'];
		if($cfg_multi_site=='Y'){
			if(!eregi("^http://",$row['litpic'])){
				$row['litpic'] = $cfg_mainsite.$row['litpic'];
			}
		}
		$news[] = $row;
	}

	//企业新闻 end

	//获取企业产品start
	$query = "Select arc.*,tp.typename
			From `#@__full_search` arc left join #@__arctype tp on tp.ID = arc.typeid
			where arc.mid='{$spaceInfos['ID']}' and arc.channelid=5 order by arc.aid desc limit 0,6;
		";
	//echo $query;exit;
	$dsql->SetQuery($query);
	$dsql->Execute();
	$products = array();
	while($row = $dsql->GetArray()){
		$row['arcurl'] = $row['url'];
		if($cfg_multi_site=='Y'){
			if(!eregi("^http://",$row['litpic'])){
				$row['litpic'] = $cfg_mainsite.$row['litpic'];
			}
		}
		$products[] = $row;
	}
	//print_r($products);
	//企业产品 end

	//供求信息start
	$query = "Select arc.*,tp.typedir,tp.typename,tp.isdefault,tp.defaultname,
			tp.namerule,tp.namerule2,tp.ispart,tp.moresite,tp.siteurl
			From `#@__infos` arc left join #@__arctype tp on arc.typeid=tp.ID
			where arc.memberID='$spaceInfos[ID]' order by arc.senddate desc limit 0,6;
	";
	$dsql->SetQuery($query);
	$dsql->Execute();
	$infos = array();
	while($row = $dsql->GetArray()){
		$row['arcurl'] = GetFileUrl($row['ID'],$row['typeid'],$row['senddate'],
			$row['title'],$row['ismake'],$row['arcrank'],$row['namerule'],
			$row['typedir'],$row['money'],true,$row['siteurl']);
		if($cfg_multi_site=='Y'){
			if(!eregi("^http://",$row['litpic'])){
				$row['litpic'] = $cfg_mainsite.$row['litpic'];
			}
		}
		$infos[] = $row;
	}
	//print_r($infos);
	//供求信息 end

	//人才招聘 start
	$query = "Select * From `#@__jobs` where memberID='{$spaceInfos['ID']}' order by pubdate desc limit 0,6;
		";
	//echo $query;exit;
	$dsql->SetQuery($query);
	$dsql->Execute();
	$jobs = array();
	while($row = $dsql->GetArray()){
		$row['arcurl'] = "index.php?uid=$uid&type=job&jobid={$row['id']}";
		$jobs[] = $row;
	}
	//print_r($jobs);
	//人才招聘 end

	require_once(dirname(__FILE__)."/templets/company/company_index.htm");
}elseif($type == 'cominfo') {
	require_once(dirname(__FILE__)."/templets/company/company_about.htm");
}elseif($type == 'culture') {
	require_once(dirname(__FILE__)."/templets/company/company_culture.htm");
}elseif($type == 'news') {
	require_once(dirname(__FILE__)."/../include/pub_datalist_dm.php");
	$channelid = 1;
	$whereSql = " arc.mid='$spaceInfos[ID]'";
	if(!empty($channelid)) $whereSql .= " And arc.channelid='$channelid' ";
	if(!empty($mtype)) $whereSql .= " And (arc.mtype='$mtype') ";
	if(!empty($keyword)){
		$whereSql .= " And (arc.title like '%$keyword%') ";
	}
	$query = "
		Select arc.*,tp.typename
		From `#@__full_search` arc left join #@__arctype tp on tp.ID = arc.typeid
		where $whereSql order by arc.aid desc
	";

	$dlist = new DataList();
	$dlist->pageSize = 10;
	$dlist->SetParameter("keyword",$keyword);
	$dlist->SetParameter("mtype",$mtype);
	$dlist->SetParameter("channelid",$channelid);
	$dlist->SetSource($query);
	require_once(dirname(__FILE__)."/templets/company/company_news.htm");
	$dlist->Close();
	exit();
}elseif($type == 'info') {
	require_once(dirname(__FILE__)."/../include/pub_datalist_dm.php");
	$channelid = -2;
	$whereSql = " arc.memberID='$spaceInfos[ID]'";
	if(!empty($channelid)) $whereSql .= " And arc.channel='$channelid' ";
	if($keyword!=""){
		$whereSql .= " And (arc.title like '%$keyword%') ";
	}
	$query = "
		Select arc.*,tp.typedir,tp.typename,tp.isdefault,tp.defaultname,
		tp.namerule,tp.namerule2,tp.ispart,tp.moresite,tp.siteurl
		From #@__infos arc left join #@__arctype tp on arc.typeid=tp.ID
		where $whereSql order by arc.senddate desc
	";

	$dlist = new DataList();
	$dlist->pageSize = 10;
	$dlist->SetParameter("keyword",$keyword);
	$dlist->SetParameter("channelid",$channelid);
	$dlist->SetSource($query);
	require_once(dirname(__FILE__)."/templets/company/company_info.htm");
	$dlist->Close();
	exit();
}elseif($type == 'product') {
	require_once(dirname(__FILE__)."/../include/pub_datalist_dm.php");
	$channelid = 5;
	$comid = $spaceInfos['ID'];
	$whereSql = " arc.memberID='$spaceInfos[ID]'";
	if(!empty($channelid)) $whereSql .= " And arc.channel='$channelid' ";

	$query = "
		Select arc.*,tp.typedir,tp.typename,tp.isdefault,tp.defaultname,
		tp.namerule,tp.namerule2,tp.ispart,tp.moresite,tp.siteurl
		From `#@__archives` arc left join #@__arctype tp on arc.typeid=tp.ID
		where $whereSql order by arc.senddate desc
	";

	$dlist = new DataList();
	$dlist->pageSize = 10;
	$dlist->SetParameter("keyword",$keyword);
	$dlist->SetParameter("channelid",$channelid);
	$dlist->SetSource($query);
	require_once(dirname(__FILE__)."/templets/company/company_product.htm");
	$dlist->Close();
	exit();
}elseif($type == 'job') {
	if(!empty($jobid)){
		$jobid = intval($jobid);
		if($jobid < 1){
			ShowMsg("id错误！","-1");
			exit();
		}
		$jobinfo = $dsql->getone("select * from #@__jobs where id=$jobid");
		$jobinfo['pubdate'] = GetDateMk($jobinfo['pubdate']);
		$jobinfo['endtime'] = GetDateMk($jobinfo['endtime']);
		if($jobinfo['salaries'] == 0){
			$jobinfo['salaries'] = '面议';
		}
		include(dirname(__FILE__)."/templets/company/company_job_view.htm");
		if(isset($dsql) && is_object($dsql)) $dsql->Close();
	}else{
		require_once(dirname(__FILE__)."/../include/pub_datalist_dm.php");
		$whereSql = " memberID='$spaceInfos[ID]'";
		$query = "Select * From #@__jobs where $whereSql order by pubdate desc";
		$dlist = new DataList();
		$dlist->pageSize = 10;
		$dlist->SetSource($query);
		require_once(dirname(__FILE__)."/templets/company/company_job.htm");
		$dlist->Close();
		if(isset($dsql) && is_object($dsql)) $dsql->Close();
	}
}elseif($type == 'guestbook') {
	require_once(dirname(__FILE__)."/templets/company/company_online.htm");
	if(isset($dsql) && is_object($dsql)) $dsql->Close();
	exit();
}elseif($type == 'contact') {
	require_once(dirname(__FILE__)."/templets/company/company_contact.htm");
	if(isset($dsql) && is_object($dsql)) $dsql->Close();
	exit();
}

?>