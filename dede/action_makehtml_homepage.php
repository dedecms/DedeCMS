<?
require_once(dirname(__FILE__)."/config.php");
require_once(dirname(__FILE__)."/../include/inc_arcpart_view.php");
if($dopost=="view")
{
	$pv = new PartView();
	$pv->SetTemplet($cfg_basedir.$cfg_templets_dir."/".$templet);
	$pv->Display();
	$pv->Close();
}
else if($dopost=="make")
{
	$homeFile = dirname(__FILE__)."/".$position;
	$homeFile = str_replace("\\","/",$homeFile);
	$homeFile = str_replace("//","/",$homeFile);
	$fp = fopen($homeFile,"w") or die("你指定的文件名有问题，无法创建文件");
	fclose($fp);
	if($saveset==1)
	{
		$dsql = new DedeSql(false);
		$dsql->SetQuery("update #@__homepageset set templet='$templet',position='$position' ");
		$dsql->ExecuteNoneQuery();
		$dsql->Close();
	}
	$pv = new PartView();
	$pv->SetTemplet($cfg_basedir.$cfg_templets_dir."/".$templet);
	$pv->SaveToHtml($homeFile);
	$pv->Close();
	echo "成功更新主页HTML：".$homeFile;
	echo "<br/><br/><a href='$position' target='_blank'>浏览...</a>";
}
?>