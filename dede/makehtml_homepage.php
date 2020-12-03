<?php
require_once(dirname(__FILE__)."/config.php");
CheckPurview('sys_MakeHtml');
require_once(DEDEINC."/arc.partview.class.php");
if(empty($dopost))
{
	$dopost = '';
}

if($dopost=="view")
{
	$pv = new PartView();
	$templet = str_replace("{style}",$cfg_df_style,$templet);
	$pv->SetTemplet($cfg_basedir.$cfg_templets_dir."/".$templet);
	$pv->Display();
	exit();
}
else if($dopost=="make")
{
	$remotepos = empty($remotepos)? '/index.html' : $remotepos;
	$isremote = empty($isremote)? 0 : $isremote;
	$serviterm = empty($serviterm)? "" : $serviterm;
	$homeFile = DEDEADMIN."/".$position;
	$homeFile = str_replace("\\","/",$homeFile);
	$homeFile = str_replace("//","/",$homeFile);
	$fp = fopen($homeFile,"w") or die("你指定的文件名有问题，无法创建文件");
	fclose($fp);
	if($saveset==1)
	{
		$iquery = "update `#@__homepageset` set templet='$templet',position='$position' ";
		$dsql->ExecuteNoneQuery($iquery);
	}
	$templet = str_replace("{style}",$cfg_df_style,$templet);
	$pv = new PartView();
	$GLOBALS['_arclistEnv'] = 'index';
	$pv->SetTemplet($cfg_basedir.$cfg_templets_dir."/".$templet);
	$pv->SaveToHtml($homeFile);
	
	echo "成功更新主页HTML：".$homeFile."<br />";
	if($serviterm ==""){
	  $config=array();
	}else{
		list($servurl,$servuser,$servpwd) = explode(',',$serviterm);
		$config=array( 'hostname' => $servurl, 'username' => $servuser, 'password' => $servpwd,'debug' => 'TRUE');
	}
	//如果启用远程站点则上传
  if($cfg_remote_site=='Y')
  {
  	if($ftp->connect($config) && $isremote == 1)
  	{
   	  if($ftp->upload($position, $remotepos, 'ascii')) echo "远程发布成功!"."<br />";
    }
  }
	echo "<a href='$position' target='_blank'>浏览...</a>";
	exit();
}
$row  = $dsql->GetOne("Select * From #@__homepageset");
include DedeInclude('templets/makehtml_homepage.htm');

?>