<?php 

if(!is_file(dirname(__FILE__)."/include/config_base.php")){
  echo "<meta http-equiv=content-type content='text/html; charset=utf-8'>\r\n";
  echo "·如果你还没安装本程序，请运行<a href='install/index.php'> install/index.php 进入安装&gt;&gt; </a><br/><br/>";
  echo "&nbsp;&nbsp;<a href='http://www.dedecms.com' style='font-size:12px' target='_blank'>Power by DedeCms OX V5.1 UTF-8版 &nbsp;织梦内容管理系统</a>";
  exit();
}

require_once(dirname(__FILE__)."/include/config_base.php");
require_once(dirname(__FILE__)."/include/inc_arcpart_view.php");

$dsql = new DedeSql(-100);
$row  = $dsql->GetOne("Select * From #@__homepageset");
$dsql->Close();
$row['templet'] = str_replace("{style}",$cfg_df_style,$row['templet']);
$pv = new PartView();
$pv->SetTemplet($cfg_basedir."/".$cfg_templets_dir."/".$row['templet']);
$pv->Display();
$pv->Close();
//ookk
?>