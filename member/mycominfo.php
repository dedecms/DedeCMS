<?php
require_once(dirname(__FILE__)."/config.php");
require_once(dirname(__FILE__)."/com_config.php");
CheckRank(0,0);
$step = (empty($step) ? '' : $step);
$db=new DedeSql(false);
if(empty($action))
{
	$cominfo = $db->GetOne("select m.email,cominfo.* from #@__member m left join #@__member_cominfo cominfo on cominfo.id=m.ID where m.ID='".$cfg_ml->M_ID."'");

	$sql = "select * from #@__sectors";
	$db->SetQuery($sql);
	$db->Execute();
	$topsectors = $subsectors = array();
	while($sector = $db->GetArray())
	{
		if($sector['reid'] == 0) {
			$topsectors[] = $sector;
		} else {
			$subsectors[] = $sector;
		}
	}
	$sectorcache = "topsectors=new Array();\n\n";
	$typeid1name = $typeid2name = '-不限-';
	foreach($topsectors as $topkey => $topsector)
	{
		if($topsector['id'] == $cominfo['typeid1'])
		{
			$typeid1name = $topsector['name'];
		}
		$sectorcache .= "topsectors[$topkey]=".'"'.$topsector['id'].'~'.$topsector['name'].'";'."\n";
		$sectorcache .= "\t".'subsectors'.$topsector['id'].'=new Array();'."\n";
		$arrCount = 0;
		foreach($subsectors as $subkey => $subsector)
		{
			if($subsector['id'] == $cominfo['typeid2'])
			{
				$typeid2name = $subsector['name'];
			}
			if($subsector['reid'] == $topsector['id'])
			{
				$sectorcache .= "\t".'subsectors'.$topsector['id'].'['.$arrCount.']="'.$subsector['id'].'~'.$subsector['name'].'";'."\n";
				$arrCount++;
			}

		}
	}

	//////////////////////地区数据处理s/////////////////////////////
		$sql = "select * from #@__area";
		$db->SetQuery($sql);
		$db->Execute();
		$toparea = $subarea = array();
		while($sector = $db->GetArray())
		{
			if($sector['reid'] == 0)
			{
				$toparea[] = $sector;
			}else
			{
				$subarea[] = $sector;
			}
		}
		$areacache = "toparea=new Array();\n\n";
		$areaidname = $areaid2name = '-不限-';
		foreach($toparea as $topkey => $topsector)
		{
			if($topsector['id'] == $cominfo['areaid'])
			{
				$areaidname = $topsector['name'];
			}
			$areacache .= "toparea[$topkey]=".'"'.$topsector['id'].'~'.$topsector['name'].'";'."\n";
			$areacache .= "\t".'subareas'.$topsector['id'].'=new Array();'."\n";
			$arrCount = 0;
			foreach($subarea as $subkey => $subsector)
			{
				if($subsector['id'] == $cominfo['areaid2'])
				{
					$areaid2name = $subsector['name'];
				}
				if($subsector['reid'] == $topsector['id'])
				{
					$areacache .= "\t".'subareas'.$topsector['id'].'['.$arrCount.']="'.$subsector['id'].'~'.$subsector['name'].'";'."\n";
					$arrCount++;
				}

			}
		}
		//echo $areacache;exit;
	//////////////////////地区数据处理e/////////////////////////////
	require_once(dirname(__FILE__)."/templets/mycominfo.htm");

}elseif($action == 'editcominfo')
{

	$svali = GetCkVdValue();
	if(strtolower($vdcode) != $svali || $svali == '') {
		ShowMsg("验证码错误！","-1");
		exit();
	}
	if($email != '' && $email != $oldemail){
		$email = trim($email);
		$query = "update #@__member set email='$email' where ID='{$cfg_ml->M_ID}'";
		$db->ExecuteNoneQuery($query);
	}
	if(!isset($sectorchange)) {
		$typeid1 = $oldtypeid1;
		$typeid2 = $oldtypeid2;
	}
	if(!isset($areachange)){
		$areaid = $oldareaid;
		$areaid2 = $oldareaid2;
	}
	$truename = filterscript(trim($truename));
	$business = filterscript(trim($business));
	$phone = filterscript($phone);
	$fax = filterscript($fax);
	$mobi = filterscript($mobi);
	$comname = filterscript(trim($comname));
	$regyear = intval($regyear);
	$regyear = max($regyear, 1000);
	$service = filterscript(trim($service));
	$typeid1 = intval($typeid1);
	$typeid2 = intval($typeid2);
	$areaid = intval($areaid);
	$areaid2 = intval($areaid2);
	$comaddr = filterscript(trim($comaddr));
	$cominfo = filterscript(trim($cominfo));
	$postid = max(filterscript($postid),18);
	$website = filterscript(trim($website));
	if($comname == '' || $regyear < 1000){
		ShowMsg("公司名称或注册年份为空，请填写完整！","-1");
		exit();
	}

	$sql = "update #@__member_cominfo set truename='$truename', business='$business',
	phone='$phone', fax='$fax', mobi='$mobi', comname='$comname',
	regyear='$regyear', areaid='$areaid', areaid2='$areaid2', service='$service', typeid1='$typeid1',
	typeid2='$typeid2', comaddr='$comaddr', cominfo='$cominfo',
	postid='$postid', website='$website' where id='{$cfg_ml->M_ID}'";

	$db = new DedeSql(false);
	$row = $db->getone("select id from #@__member_cominfo where  id='{$cfg_ml->M_ID}'");
	if($row['id'] < 1){
		$db->setquery("insert into #@__member_cominfo (id) values(".$cfg_ml->M_ID.")");
		$db->ExecuteNoneQuery();
	}
	$db->SetQuery($sql);//exit;
	if(!$db->ExecuteNoneQuery()) {
		echo $db->GetError();
		$db->Close();
		ShowMsg("更改企业资料出错，请检查输入是否合法！","-1");
		exit();
	} else {
		$db->Close();
		ShowMsg("成功更新企业资料，请进一步完善企业文化","mycominfo.php?action=culture");
		exit();
	}
}elseif($action == 'culture')
{
	if($step != 2)
	{
		$cominfo = $db->GetOne("select culture from #@__member_cominfo where id=$cfg_ml->M_ID;");
		require_once(dirname(__FILE__)."/templets/mycominfo.htm");
	}else{
		$culture = filterscript(trim($culture));
		$sql = "update #@__member_cominfo set culture='$culture' where id={$cfg_ml->M_ID};";
		if(!$db->ExecuteNoneQuery($sql))
		{
			$db->Close();
			ShowMsg("更改企业文化出错，请检查输入是否合法！","-1");
			exit();
		}else{
			$db->Close();
			ShowMsg("成功更新企业文化！","mycominfo.php?action=culture");
			exit();
		}
	}
}

?>