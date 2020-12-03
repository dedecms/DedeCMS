<?
require("config.php");
$query = "Select * From $baktable";
if($baktype=="tid")
{
	if($tidstart=="")
	{
		ShowMsg("你没指定起始ID！",-1);
		exit;
	}
	$query .= " where ID>=$tidstart";
	if($tidend!="") $query .= " And ID<$tidend";
}
else if($baktype=="stime")
{
	if($timestart=="")
	{
		ShowMsg("你没指定起始时间！",-1);
		exit;
	}
	if($baktable!="dede_member")
	{
		$query .= " where dtime>='$timestart'";
		if($timeend!="") $query .= " And dtime<'$timeend'";
	}
	else
	{
		$query .= " where jointime>='$timestart'";
		if($timeend!="") $query .= " And jointime<'$timeend'";
	}
}
if($filesize=="") $filesize=0;
$bufflength = $filesize*1024*1024;
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
$baktable = trim($baktable);
$filename = $bakfulldir."/".$baktable;
//--获取表的字段总数
$rs = mysql_list_fields($dbname,$baktable,$conn);
$i=0;
$f=1;
$fs="";
while($row=mysql_fetch_field($rs))
{
		$fs[$i] = trim($row->name);
		$i++;
}
$fsd = count($fs);
//---------------------------
$fp = fopen($filename.".txt","w");
$rs2 = mysql_query($query,$conn);
$dd = mysql_num_rows($rs2);
$line = "";
while($row2 = mysql_fetch_array($rs2))
{
	$line .= "~Insert Into $baktable(";
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
	if($baktype=="fsize")
	{
		if(strlen($line)>$bufflength)
		{
			fwrite($fp,$line);
			fclose($fp);
			echo "备份: $baktable --&gt; $filename".$f.".txt OK <br>\r\n";
			$line = "";
			$f++;
			$fp = fopen($filename.$f.".txt","w");
		}
	}
	else
	{
		fwrite($fp,$line);
		$line = "";
	}
}
@fclose($fp);
if($baktype!="fsize") echo "备份: $baktable ( $dd ) --&gt; $filename".".txt OK <br>\r\n";
?>
</td>
</tr>
</table>
</body>
</html>