<?php 
require_once(dirname(__FILE__)."/config.php");
CheckPurview('sys_MakeHtml');
require_once(dirname(__FILE__)."/../include/inc_arcpart_view.php");
if(empty($typeid)) $typeid = 0;
if(empty($templet)) $templet = "plus/js.htm";
if(empty($uptype)) $uptype = "all";
if($uptype == "all")
{
  $dsql = new DedeSql(false);
  $row = $dsql->GetOne("Select ID From #@__arctype where ID>'$typeid' And ispart<>2 order by ID asc limit 0,1;");
  $dsql->Close();
  if(!is_array($row)){
	  ShowMsg("完成所有文件更新！","javascript:;");
	  exit();
  }
  else{
	  $pv = new PartView($row['ID']);
    $pv->SetTemplet($cfg_basedir.$cfg_templets_dir."/".$templet);
    $pv->SaveToHtml($cfg_basedir."/data/js/".$row['ID'].".js");
    $pv->Close();
	  $typeid = $row['ID'];
	  ShowMsg("成功更新"."/data/js/".$row['ID'].".js，继续进行操作！","makehtml_js_action.php?typeid=$typeid",0,100);
    exit();
  }
}
else
{
	$pv = new PartView($typeid);
  $pv->SetTemplet($cfg_basedir.$cfg_templets_dir."/".$templet);
  $pv->SaveToHtml($cfg_basedir."/data/js/".$typeid.".js");
  $pv->Close();
  echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>\r\n";
  echo "成功更新"."/data/js/".$typeid.".js！";
  echo "预览：";
  echo "<hr>";
  echo "<script src='../data/js/".$typeid.".js'></script>";
  exit();
}

ClearAllLink();
?>