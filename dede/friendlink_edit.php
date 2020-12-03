<?php 
require_once(dirname(__FILE__)."/config.php");
CheckPurview('plus_友情链接模块');
if(!empty($_COOKIE['ENV_GOBACK_URL'])) $ENV_GOBACK_URL = $_COOKIE['ENV_GOBACK_URL'];
else $ENV_GOBACK_URL = friendlink_main.php;

if(empty($dopost)) $dopost = "";

if(isset($allid)){
	$aids = explode(',',$allid);
	if(count($aids)==1){
		$ID = $aids[0];
		$dopost = "delete";
	}
}

if($dopost=="delete")
{
	$dsql = new DedeSql(false);
	$ID = ereg_replace("[^0-9]","",$ID);
	$dsql->ExecuteNoneQuery("Delete From #@__flink where ID='$ID'");
	$dsql->Close();
	ShowMsg("成功删除一个链接！",$ENV_GOBACK_URL);
	exit();
}
else if($dopost=="delall"){
	if(isset($aids) && is_array($aids)){
	   $dsql = new DedeSql(false);
	   foreach($aids as $aid){
	   	 $aid = ereg_replace("[^0-9]","",$aid);
	   	 $dsql->ExecuteNoneQuery("Delete From #@__flink where ID='$aid'");
	   }
	   $dsql->Close();
	   ShowMsg("成功删除指定链接！",$ENV_GOBACK_URL);
	   exit();
  }else{
  	 ShowMsg("你没选定任何链接！",$ENV_GOBACK_URL);
  	 exit();
	}
}
else if($dopost=="saveedit")
{
	$dsql = new DedeSql(false);
	$ID = ereg_replace("[^0-9]","",$ID);
	$query = "Update #@__flink set 
	sortrank='$sortrank',url='$url',webname='$webname',
	logo='$logo',msg='$msg',
	email='$email',typeid='$typeid',
	ischeck='$ischeck' where ID='$ID'";
	$dsql->SetQuery($query);
	$dsql->ExecuteNoneQuery();
	$dsql->Close();
	ShowMsg("成功更改一个链接！",$ENV_GOBACK_URL);
	exit();
}
$dsql = new DedeSql(false);
$myLink = $dsql->GetOne("Select #@__flink.*,#@__flinktype.typename From #@__flink left join #@__flinktype on #@__flink.typeid=#@__flinktype.ID where #@__flink.ID=$ID");

require_once(dirname(__FILE__)."/templets/friendlink_edit.htm");

ClearAllLink();
?>