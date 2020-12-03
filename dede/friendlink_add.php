<?php 
require(dirname(__FILE__)."/config.php");
CheckPurview('plus_友情链接模块');

$dsql = new DedeSql(false);

if(empty($dopost)) $dopost="";
if($dopost=="add")
{
   $dtime = strftime("%Y-%m-%d %H:%M:%S",time());
   if(is_uploaded_file($logoimg))
   {
	   $names = split("\.",$logoimg_name);
	   $shortname = ".".$names[count($names)-1];
	   $filename = strftime("%Y%m%d%H%M%S",time()).mt_rand(1000,9999).$shortname;
	   $imgurl = $cfg_medias_dir."/flink";
	   if(!is_dir($cfg_basedir.$imgurl)){
	   	  MkdirAll($cfg_basedir.$imgurl,$GLOBALS['cfg_dir_purview']);
	   	  CloseFtp();
	   }
	   $imgurl = $imgurl."/".$filename;
	   move_uploaded_file($logoimg,$cfg_basedir.$imgurl) or die("复制文件到:".$cfg_basedir.$imgurl."失败");
	   @unlink($logoimg);
   }
   else 
	 { $imgurl = $logo; }
   $query = "Insert Into #@__flink(sortrank,url,webname,logo,msg,email,typeid,dtime,ischeck) 
   Values('$sortrank','$url','$webname','$imgurl','$msg','$email',$typeid,'$dtime','$ischeck')";
   $dsql->SetQuery($query);
   $dsql->ExecuteNoneQuery();
   if(!empty($_COOKIE['ENV_GOBACK_URL'])) $burl = $_COOKIE['ENV_GOBACK_URL'];
   else $burl = "friendlink_main.php";
   $dsql->Close();
   ShowMsg("成功增加一个链接!",$burl,0,500);
   exit();
}

require_once(dirname(__FILE__)."/templets/friendlink_add.htm");

ClearAllLink();

?>