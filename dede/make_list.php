<?
require_once("config.php");
require_once("inc_makelistcode.php");
require_once("inc_makepartcode.php");
if(!isset($job)) $job="";
?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=gb2312'>
<title>生成所有类目的文章静态列表</title>
<link href='base.css' rel='stylesheet' type='text/css'>
</head>
<body background='img/allbg.gif' leftmargin='20' topmargin='20'>
<p>
生成类目的文章静态列表<br>
<?
$conn = connectMySql();
$mp = new MakePartCode();
$mc = new MakeListCode();
if($job=="all")
{
	$rs = mysql_query("Select * From dede_arttype",$conn);
	while($row=mysql_fetch_object($rs))
	{
		if($row->isdefault!=-1)
		{
			$rs2 = mysql_query("Select ID,typeid,fname,body From dede_partmode where typeid=".$row->ID,$conn);
			$row2 = mysql_fetch_array($rs2);
			if(empty($row2["ID"]))
			{
				if($row->ispart==1)
				{
					if($row->defaultname=="") $defaultname="index.html";
					else $defaultname=$row->defaultname;
					$uname = $art_dir."/".$row->typedir."/".$defaultname;
					$fname = $base_dir.$uname;
					$modname = $base_dir.$mod_dir."/".$row->modname."/part.htm";
					$fp = fopen($modname,"r");
					$body = fread($fp,filesize($modname));
					fclose($fp);
					$mp->typeID=$row->ID;
					$mp->MakeMode($body,$fname);
					echo "<li>创建：".$row->typename." 板块：<a href='$uname' target='_blank'>$fname</a></li>";
				}
				else
				{
					echo "<li>创建：".$row->typename." 列表：</li>";
					$mc->SetType($row->ID);
					$mc->MakeList("","");
				}
			}
			else
			{
				$uname = "/".ereg_replace("^/{1,}","",$row2["fname"]);
				$fname = $base_dir.$uname;
				$body = $row2["body"];
				$mp->typeID=$row->ID;
				$mp->MakeMode($body,$fname);
				echo "<li>创建：".$row->typename." 板块：<a href='$uname' target='_blank'>$fname</a></li>";
			}
		}
		else
		{
			echo "<li>".$row->typename." 是动态列表,不创建HTML!</li>";
		}
	}
}
else
{
	if($actype=="acdefault") $starttime="";
	$typeids = split("`",$typeid);
	foreach($typeids as $typeid)
	{
	  if($typeid=="") continue;
	  $likeid = $typeid."`".$mc->GetSunIDS($typeid);
	  $likeids = split("`",$likeid);
	  foreach($likeids as $typeid)
	  {
		if($typeid=="") continue;
		$rs = mysql_query("Select * From dede_arttype where ID=$typeid",$conn);
		$row=mysql_fetch_object($rs);
		if($row->isdefault!=-1)
		{
			$rs2 = mysql_query("Select ID,typeid,fname,body From dede_partmode where typeid=".$typeid,$conn);
			$row2 = mysql_fetch_array($rs2);
			if(empty($row2["ID"]))
			{
				if($row->ispart==1)
				{
					if($row->defaultname=="") $defaultname="index.html";
					else $defaultname=$row->defaultname;
					$uname = $art_dir."/".$row->typedir."/".$defaultname;
					$fname = $base_dir.$uname;
					$modname = $base_dir.$mod_dir."/".$row->modname."/part.htm";
					$fp = fopen($modname,"r");
					$body = fread($fp,filesize($modname));
					fclose($fp);
					$mp->typeID=$typeid;
					$mp->MakeMode($body,$fname);
					echo "<li>创建：".$row->typename." 板块：<a href='$uname' target='_blank'>$fname</a></li>";
				}
				else
				{
					echo "<li>创建：".$row->typename." 列表：</li>";
					$mc->SetType($row->ID);
					$mc->MakeList("",$starttime);
				}
			}
			else
			{
				$uname = "/".ereg_replace("^/{1,}","",$row2["fname"]);
				$fname = $base_dir.$uname;
				$body = $row2["body"];
				$mp->typeID=$typeid;
				$mp->MakeMode($body,$fname);
				echo "<li>创建：".$row->typename." 板块：<a href='$uname' target='_blank'>$fname</a></li>";
			}
		}
		else
		{
			echo "<li>".$row->typename." 是动态列表,不创建HTML!</li>";
		}
	  }//foreach
	}//foreach
}
?>
</p>
<hr width='98%'>
<p>
<a href='list_type.php'>[返回频道管理页]</a>
<a href='javascript:history.go(-1);'>[返回上一页]</a>
</p>
</body>
</html>