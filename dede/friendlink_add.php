<?php
require(dirname(__FILE__)."/config.php");
CheckPurview('plus_友情链接模块');
if(empty($dopost))
{
	$dopost="";
}
if($dopost=="add")
{
	$dtime = time();
	if(is_uploaded_file($logoimg))
	{
		$names = split("\.",$logoimg_name);
		$shortname = ".".$names[count($names)-1];
		if(!eregi("(jpg|gif|png)$",$shortname))
		{
			$shortname = '.gif';
		}
		$filename = MyDate("ymdHis",time()).mt_rand(1000,9999).$shortname;
		$imgurl = $cfg_medias_dir."/flink";
		if(!is_dir($cfg_basedir.$imgurl))
		{
			MkdirAll($cfg_basedir.$imgurl,$cfg_dir_purview);
			CloseFtp();
		}
		$imgurl = $imgurl."/".$filename;
		move_uploaded_file($logoimg,$cfg_basedir.$imgurl) or die("复制文件到:".$cfg_basedir.$imgurl."失败");
		@unlink($logoimg);
	}
	else
	{
		$imgurl = $logo;
	}
	
	//强制检测用户友情链接分类是否数据结构不符
	if(empty($typeid) || ereg("[^0-9]", $typeid))
	{
		$typeid = 0;
		$dsql->ExecuteNoneQuery("ALTER TABLE `#@__flinktype` CHANGE `ID` `id` MEDIUMINT( 8 ) UNSIGNED DEFAULT NULL AUTO_INCREMENT; ");
	}
	
	$query = "Insert Into `#@__flink`(sortrank,url,webname,logo,msg,email,typeid,dtime,ischeck)
            Values('$sortrank','$url','$webname','$imgurl','$msg','$email','$typeid','$dtime','$ischeck'); ";
	$rs = $dsql->ExecuteNoneQuery($query);
	$burl = empty($_COOKIE['ENV_GOBACK_URL']) ? "friendlink_main.php" : $_COOKIE['ENV_GOBACK_URL'];
	if($rs)
	{
		ShowMsg("成功增加一个链接!",$burl,0,500);
		exit();
	}
	else
	{
		ShowMsg("增加链接时出错，请向官方反馈，原因：".$dsql->GetError(),"javascript:;");
		exit();
	}
}
include DedeInclude('templets/friendlink_add.htm');

?>