<?php 
require(dirname(__FILE__)."/config.php");
if(empty($dopost)) $dopost = "";
CheckPurview('temp_One');
//////////////////////////////////////////
if($dopost=="save")
{
	require_once(dirname(__FILE__)."/../include/inc_arcpart_view.php");
	$uptime = time();
	$body = str_replace('&quot;','\\"',$body);
	$filename = ereg_replace("^/","",$nfilename);
	
	$inQuery = "
	 Insert Into #@__sgpage(title,ismake,filename,uptime,body)
	 Values('$title','$ismake','$filename','$uptime','$body');
	";
	$dsql = new DedeSql(false);
	$dsql->SetQuery($inQuery);
	if(!$dsql->ExecuteNoneQuery())
	{
		$dsql->Close();
		ShowMsg("增加页面失败，请检查长相是否有问题！","-1");
	  exit();
	}
	$dsql->Close();
	
	$filename = $cfg_basedir.$cfg_cmspath."/".$filename;
	
	if($ismake==1)
	{
	  $pv = new PartView();
    $pv->SetTemplet(stripslashes($body),"string");
    $pv->SaveToHtml($filename);
    $pv->Close();
  }
  else
  {
  	$fp = fopen($filename,"w") or die("创建：{$filename} 失败，可能是没有权限！");
  	fwrite($fp,stripslashes($body));
  	fclose($fp);
  }
	ShowMsg("成功增加一个页面！","templets_one.php");
	exit();
}

require_once(dirname(__FILE__)."/templets/templets_one_add.htm");

ClearAllLink();
?>