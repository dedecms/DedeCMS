<?
require("config.php");
$conn = connectMySql();
?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=gb2312'>
<title>数据还原</title>
<link href='base.css' rel='stylesheet' type='text/css'>
</head>
<body background='img/allbg.gif' leftmargin='8' topmargin='8'>
<table width="99%" border="0" cellpadding="3" cellspacing="1" bgcolor="#666666">
  <tr>
    <td height="19" bgcolor="#E7E7E7"> &nbsp;<b>数据还原</b>&nbsp; [<a href="javascript:history.go(-1);"><u>返回上一页</u></a>]</td>
</tr>
<tr>
    <td height="215" bgcolor="#FFFFFF">
<?
if(!is_dir($base_dir.$bak_dir)) @mkdir($base_dir.$bak_dir,0777);
////////保存出錯的SQL語句
$errFile = $base_dir.$bak_dir."/error";
if(!is_dir($errFile)) @mkdir($errFile,0777);
$errFile = $errFile."/err.txt";
$refiles = ereg_replace("^,","",$refiles);
$refiles = split("\*",$refiles);
$fnum = count($refiles);
$fperr = fopen($errFile,"w");
for($i=0;$i<$fnum;$i++)
{
	$filename = $refiles[$i];
	$filename = $base_dir.$bak_dir."/".$filename;
	if(!file_exists($filename)||is_dir($filename)) continue;
	$fp = fopen($filename,"r");
	$j = 0;
	$query = fgets($fp,1024);
	while(!feof($fp))
	{
		$line = fgets($fp,1024);
		if(ereg("^~Insert",$line))
		{
			$query = trim(ereg_replace("^~","",$query));
			@mysql_query($query,$conn);
			if(mysql_error()=="") $j++;
			else fwrite($fperr,$query."\r\n");
			$query = "";
			$query.=$line;
		}
		else
		{
			$query.=$line;
		}
	}
	//插入最后一笔记录
	$query = trim(ereg_replace("^~","",$query));
	@mysql_query($query,$conn);
	if(mysql_error()=="") $j++;
	else fwrite($fperr,$query."\r\n");
	//输出完成提示
	echo $filename." OK 共: $j 条记录已插入!<br>\r\n";
	fclose($fp);
}
//////////////
fclose($fperr);
?>
</td>
</tr>
</table>
</body>
</html>