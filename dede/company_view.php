<?php
require(dirname(__FILE__)."/config.php");
CheckPurview('member_Edit');
if(!isset($_COOKIE['ENV_GOBACK_URL'])) $ENV_GOBACK_URL = "";
else $ENV_GOBACK_URL="member_main.php";
$ID = ereg_replace("[^0-9]","",$ID);
$dsql = new DedeSql(false);
$row=$dsql->GetOne("select  m.*, mc.* from #@__member m left join #@__member_cominfo mc on mc.id=m.ID where m.ID='$ID'");

	$sql = "select * from #@__sectors";
	$dsql->SetQuery($sql);
	$dsql->Execute();
	$topsectors = $subsectors = array();
	while($sector = $dsql->GetArray())
	{
		if($sector['reid'] == 0) {
			$topsectors[] = $sector;
		} else {
			$subsectors[] = $sector;
		}
	}
	$sectorcache = "<!--\ntopsectors=new Array();\n\n";
	$typeid1name = $typeid2name = '-不限-';
	foreach($topsectors as $topkey => $topsector)
	{
		if($topsector['id'] == $row['typeid1'])
		{
			$typeid1name = $topsector['name'];
		}
		$sectorcache .= "topsectors[$topkey]=".'"'.$topsector['id'].'~'.$topsector['name'].'";'."\n";
		$sectorcache .= "\t".'subsectors'.$topsector['id'].'=new Array();'."\n";
		foreach($subsectors as $subkey => $subsector)
		{
			if($subsector['id'] == $row['typeid2'])
			{
				$typeid2name = $subsector['name'];
			}
			if($subsector['reid'] == $topsector['id'])
			{//B1[0]="101~东城区";
				$sectorcache .= "\t".'subsectors'.$topsector['id'].'['.$subkey.']="'.$subsector['id'].'~'.$subsector['name'].'";'."\n";
			}

		}
	}
	$sectorcache .= '-->';
	
	
	require_once(dirname(__FILE__)."/templets/company_view.htm");
	
	ClearAllLink();
	
?>