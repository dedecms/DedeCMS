<?php 
require_once(dirname(__FILE__)."/config.php");
@set_time_limit(1800);

if(empty($dopost)) $dopost = "";
if(empty($reset)) $reset = "";

header("Content-Type: text/html; charset={$cfg_ver_lang}");

//用户Action
if($dopost=="yes"){
  if($reset=='yes'){
  	$dsql = new DedeSql(false);
  	$dsql->ExecuteNoneQuery("Update #@__archives set templet='' where channel<>-1");
  	$dsql->Close();
  	echo "完成还原处理！ Action has finish";
  }else{
		$dsql = new DedeSql(false);
		
		if($autotype=='empty'){
			$addquery = " And templet='' ";
		}else if($autotype=='hand'){
			if(!empty($startid)) $addquery .= " And ID>=$startid ";
			if(!empty($endid)) $addquery .= " And ID<=$endid ";
		}else{
			$addquery = "";
		}
		$okquery = "Select ID From #@__archives where channel='$channeltype' $addquery ";
		$dsql->SetQuery($okquery);
		$dsql->Execute();
		while($row = $dsql->GetArray()){
			$temparticleok = addslashes(str_replace('{rand}',mt_rand($rndstart,$rndend),$temparticle));
			$dsql->ExecuteNoneQuery("Update #@__archives set templet='$temparticleok' where ID='{$row['ID']}' ");
		}
		
  	$dsql->Close();
  	echo "完成处理！";
	}
	exit();
}
//读取所有模型资料
$dsql = new DedeSql(false);
$dsql->SetQuery("select * from #@__channeltype where ID>-1 And isshow=1 order by ID");
$dsql->Execute();
while($row=$dsql->GetObject()){
  $channelArray[$row->ID]['typename'] = $row->typename;
  $channelArray[$row->ID]['nid'] = $row->nid;
}

require_once(dirname(__FILE__)."/templets/rnd_templets_main.htm");

ClearAllLink();
?>
