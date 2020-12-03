<?
if(is_dir(dirname(__FILE__)."/setup"))
{
  echo "如果你还没安装本程序，请运行<a href='setup/index.php'> setup/index.php </a>,否则请删除这个文件夹!";
  exit();
}
require_once(dirname(__FILE__)."/include/config_base.php");
require_once(dirname(__FILE__)."/include/inc_arcpart_view.php");
$dsql = new DedeSql(false);
$row  = $dsql->GetOne("Select * From #@__homepageset");
$dsql->Close();
$pv = new PartView();
$pv->SetTemplet($cfg_basedir."/".$cfg_templets_dir."/".$row['templet']);
$pv->Display();
$pv->Close();
?>