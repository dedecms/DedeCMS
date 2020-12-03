<?
require("config.php");
$conn = connectMySql();
?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=gb2312'>
<title>数据备份</title>
<link href='base.css' rel='stylesheet' type='text/css'>
</head>
<body background='img/allbg.gif' leftmargin='8' topmargin='8'>
<table width="99%" border="0" cellpadding="3" cellspacing="1" bgcolor="#666666">
  <tr>
    <td height="19" background="img/tbg.gif"> &nbsp;<b>数据备份</b>&nbsp; [<a href="javascript:history.go(-1);"><u>返回上一页</u></a>]</td>
</tr>
<tr>
    <td height="215" bgcolor="#FFFFFF">
<?
$bakfulldir = $base_dir.$bak_dir;
if(!is_dir($bakfulldir)) @mkdir($bakfulldir,$dir_purview);
if($baktable!="0")
{
	$baktable = trim($baktable);
	$filename = $bakfulldir."/".$baktable.".txt";
	//--获取表的字段总数
	$rs = mysql_list_fields($dbname,$baktable,$conn);
	$i=0;
	$fs="";
	while($row=mysql_fetch_field($rs))
	{
		$fs[$i] = trim($row->name);
		$i++;
	}
	$fsd = count($fs);
	//---------------------------
	$fp = fopen($filename,"w");
	$rs2 = mysql_query("Select * From $baktable",$conn);
	$dd = mysql_num_rows($rs2);
	while($row2 = mysql_fetch_array($rs2))
	{
	    $line = "~Insert Into $baktable(";
	    for($i=0;$i<$fsd;$i++)
		{
			if($i<$fsd-1)
				$line.=trim($fs[$i]).",";
			else
				$line.=trim($fs[$i]).")";
		}
		$line .= " Values(";
		for($i=0;$i<$fsd;$i++)
		{
			if($i<$fsd-1)
				$line.="'".addslashes(trim($row2[$fs[$i]]))."',";
			else
				$line.="'".addslashes(trim($row2[$fs[$i]]))."');\r\n";
		}
		fwrite($fp,$line);
	}
	fclose($fp);
	echo "备份: $baktable ( $dd ) --&gt; $filename OK <br>\r\n";
}
else
{
	$rs = mysql_list_tables($dbname,$conn);
	while($row = mysql_fetch_array($rs))
	{
		$filename = $bakfulldir."/".$row[0].".txt";
		//--获取表的字段总数
		$rsd = mysql_list_fields($dbname,$row[0],$conn);
		$i=0;
		$fs="";
		while($rowd=mysql_fetch_field($rsd))
		{
			$fs[$i] = trim($rowd->name);
			$i++;
		}
		$fsd = count($fs);
		//---------------------------
		$fp = fopen($filename,"w");
		$rs2 = mysql_query("Select * From ".trim($row[0]),$conn);
		$dd = mysql_num_rows($rs2);
		while($row2 = mysql_fetch_array($rs2))
		{
			$line = "~Insert Into ".$row[0]."(";
			for($i=0;$i<$fsd;$i++)
			{
				if($i<$fsd-1)
					$line.=$fs[$i].",";
				else
					$line.=$fs[$i].")";
			}
			$line.=" Values(";
			for($i=0;$i<$fsd;$i++)
			{
				if($i<$fsd-1)
					$line.="'".addslashes(trim($row2[$fs[$i]]))."',";
				else
					$line.="'".addslashes(trim($row2[$fs[$i]]))."');\r\n";
			}
			fwrite($fp,$line);
		}
		fclose($fp);
		echo "备份: ".$row[0]." ( $dd ) --&gt; $filename OK <br>\r\n";
	}
}
?>
</td>
</tr>
</table>
</body>
</html>