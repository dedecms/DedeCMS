<?
require_once(dirname(__FILE__)."/config.php");
if(empty($dopost)) $dopost="";
@ob_start();
@set_time_limit(3600);
if(!is_dir($cfg_basedir.$cfg_backup_dir)) mkdir($cfg_basedir.$cfg_backup_dir,0777);
//备份文件的最大大小(默认是512K),超过这个大小会创建新的文件一直到备份完毕
$maxLen = 500*1024;
//--------------------------------------------
//数据备份
/*
function __bakData()
*/
//---------------------------------------------
if($dopost=="bakdata")
{
  $dsql = new DedeSql(false);
  $bakdir = $cfg_basedir.$cfg_backup_dir."/";
  $fileNo = 1;
  $bakStr = "";
  if(!is_dir($bakdir)) mkdir($bakdir,$cfg_dir_purview);
  //备份数据结构信息
  $dsql->SetQuery("Show Tables");
  $dsql->Execute('t');
	while($row = $dsql->GetArray('t'))
	{
		 $bakStr .= "~sql:DROP TABLE IF EXISTS `$baktable`;\r\n\r\n";
	   $dsql->SetQuery("SHOW CREATE TABLE ".$dsql->dbName.".".$baktable);
     $dsql->Execute();
     $row2 = $dsql->GetArray();
     $bakStr .= "~sql:".$row2[1].";\r\n\r\n";
  }
	$dsql->SetQuery("Show Tables");
  $dsql->Execute('t');
  $fp = fopen($bakdir."baktable".$fileNo.".txt","w");
	fwrite($fp,$bakStr);
	fclose($fp);
	echo "成功备份数据结构信息！<br/>\r\n";
	flush();
	$fileNo++;
	$bakStr = "";
	//--------------------
	//备份数据记录
	//----------------------	 
	while($row = $dsql->GetArray('t'))
	{
		 $baktable = $row[0];
		 //获取字段信息
		 //---------------------------------
		 $j=0;
	   $fs="";
		 $dsql->GetTableFields($baktable);
	   $intable = "~sql:Insert Into $baktable(";
	   while($r = $dsql->GetFieldObject()){
	   	 $fs[$j] = trim($r->name);
	   	 $intable .= $fs[$j].",";
	   	 $j++;
	   }
	   $fsd = $j-1;
	   $intable = ereg_replace(",$","",$intable).") Values(";
	   //读取表里的内容
	   //-----------------------------------------
	   $dsql->SetQuery("Select * From $baktable");
	   $dsql->Execute();
	   while($row2 = $dsql->GetArray())
	   {
		     $line = $intable;
		     for($j=0;$j<=$fsd;$j++){
			     if($j < $fsd) $line .= "'".addslashes($row2[$fs[$j]])."',";
			     else $line .= "'".addslashes($row2[$fs[$j]])."');\r\n";
		     }
		     if(strlen($bakStr) < $maxLen){ $bakStr .= $line;  }
		     else{
		     	 $fp = fopen($bakdir."baktable".$fileNo.".txt","w");
		     	 fwrite($fp,$bakStr);
		     	 fclose($fp);
		     	 echo "保存到第 $fileNo 个文件！<br/>\r\n";
		     	 flush();
		     	 $bakStr = $line;
		     	 $fileNo++;
		     }
	   }
	}//循环所有表
	
	if($bakStr!=""){
		$fp = fopen($bakdir."baktable".$fileNo.".txt","w");
		fwrite($fp,$bakStr);
		fclose($fp);
		echo "保存到第 $fileNo 个文件！<br/>\r\n";
		flush();
	}
	
	ShowMsg("成功备份所有数据表！","sys_back_data.php?".time(),0,3000);
	$dsql->Close();
	exit();
}
//---------------------------------------------
//数据还原
/*
function __reData()
*/
//---------------------------------------------
if($dopost=="redata")
{
  if(empty($userok)) $userok="";
	if($userok!="yes")
	{
	   require_once(dirname(__FILE__)."/../include/pub_oxwindow.php");
	   $wintitle = "还原数据";
	   $wecome_info = "<a href='sys_back_data.php'><u>数据还原/备份</u></a>::数据还原";
	   $win = new OxWindow();
	   $win->Init("sys_back_data.php","js/blank.js","POST");
	   $win->AddHidden("dopost",$dopost);
	   $win->AddHidden("userok","yes");
	   $win->AddTitle("系统警告！");
	   $win->AddMsgItem("还原数据前会清空现有数据库所有表的内容，你确实要还原么？","50");
	   $winform = $win->GetWindow("ok");
	   $win->Display();
	   exit();
  }
  //---------------------------
  //还原数据
  //---------------------------
  $dsql = new DedeSql(false);
  if(!is_dir($cfg_basedir.$cfg_backup_dir)) mkdir($cfg_basedir.$cfg_backup_dir,0777);
  ////////保存出錯的SQL語句
  $errFile = $cfg_basedir.$cfg_backup_dir."/error";
  if(!is_dir($errFile)) mkdir($errFile,0777);
  $errFile = $errFile."/err.txt";
  $fperr = fopen($errFile,"w");
  $dh = dir($cfg_basedir.$cfg_backup_dir);
  while($filename=$dh->Read())
  {
	  $filename = $cfg_basedir.$cfg_backup_dir."/".$filename;
	  if(is_dir($filename) || ereg("\.$",$filename)) continue;
	  $fp = fopen($filename,"r");
	  $j = 0;
	  $query = "";
	  while(!feof($fp))
	  {
		  $line = fgets($fp,1024);
		  if(eregi("^~sql:",$line) && $query!="")
		  {
			  $query = trim(eregi_replace("^~sql:","",$query));
			  $dsql->SetQuery($query);
			  if($dsql->ExecuteNoneQuery()) $j++;
			  else fwrite($fperr,$query."\r\n");
			  $query = $line;
		  }
		  else{ $query .= $line; }
	  }
	  fclose($fp);
	  $query = trim(eregi_replace("^~sql:","",$query));
	  $dsql->SetQuery($query);
	  if($dsql->ExecuteNoneQuery()) $j++;
	  else fwrite($fperr,$query."\r\n");
	  echo $filename." 有: $j 条SQL语句成功运行!<br>\r\n";
	}
	$dh->Close();
  fclose($fperr);
  ShowMsg("成功还原所有数据！","sys_back_data.php?".time(),0,3000);
  exit();
}
?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=gb2312'>
<title>备份与还原</title>
<link href='base.css' rel='stylesheet' type='text/css'>
</head>
<body background='img/allbg.gif' leftmargin='8' topmargin='8'>
<table width="99%" border="0" cellpadding="3" cellspacing="1" bgcolor="#666666">
<tr>
<td height="19" background="img/tbg.gif" bgcolor="#E7E7E7">
&nbsp;<b>数据备份</b>
</td>
</tr>
<tr>
<td height="215" align="center" bgcolor="#FFFFFF"> 
<table width="96%" border="0" cellspacing="1" cellpadding="0">
<form name="form1" action="sys_back_data.php">
<input type="hidden" name="dopost" value="bakdata">
<tr> 
<td width="25%" bgcolor="#F1F2EC"><strong>备份所有数据：</strong></td>
<td width="75%" bgcolor="#F1F2EC" align="right">
	<input type="submit" name="Submit" value="确定备份">
</td>
</tr>
<tr> 
<td height="45" align="right">数据库信息：</td>
<td>
<select name="baktable" id="baktable" style="width:250">
<?
$dsql = new DedeSql(false);
$dsql->SetQuery("Show Tables");
$dsql->Execute('t');
while($row = $dsql->GetArray('t'))
{
	$dsql->SetQuery("Select count(*) From ".$row[0]);
	$dsql->Execute('n');
	$row2 = $dsql->GetArray('n');
	$dd = $row2[0];
	echo "			<option value='".$row[0]."'>".$row[0]."(".$dd.")</option>\r\n";
}
$dsql->Close();
?>
</select>
</td>
</tr>
<tr> 
<td height="45" align="right">存放路径：</td>
<td><?=$cfg_backup_dir?></td>
</tr>
</form>
<tr align="center"> 
<td colspan="2" bgcolor="#F1F2EC"><b>数据备份说明</b></td>
</tr>
<tr> 
<td height="32" colspan="2">　　本版采用的是一键备份和一键还原的模式，如果你重装本系统，请先备份数据，安装新系统后，把备份数据上传到新系统的备份文件夹，然后执行一键还原即可，如果你的数据量太大，无法一次性还原，请先把 baktable1.txt 放到备份文件夹进行还原，然后再把其它数据分多次还原。</td>
</tr>
<form name="formbak2" action="sys_back_data.php">
<input type="hidden" name="dopost" value="redata">
 <tr> 
<td bgcolor="#F1F2EC"><b>还原数据</b></td>
<td bgcolor="#F1F2EC" align="right">
	<input type="submit" name="Submit2" value="确定还原">
</td>
</tr>
<tr>
<td height="30" align="right">备份数据存放路径：</td>
<td><?=$cfg_backup_dir?></td>
</tr>
<tr> 
<td bgcolor="#F1F2EC" colspan="2">&nbsp;</td>
</tr>
<tr> 
            <td align="right" valign="top">在备份目录的数据：</td>
            <td valign="top" align="right">&nbsp; </td>
</tr>
<tr> 
<td bgcolor="#F1F2EC" colspan="2">&nbsp;</td>
</tr>
<tr> 
<td valign="top" colspan="2"align="center">
<table width="80%" border="0" cellspacing="1" cellpadding="0">
<tr><td width="6%"></td><td></td></tr>
		<?
		$dh = dir($cfg_basedir.$cfg_backup_dir);
		while($filename = $dh->read())
		{
		 	if(!is_dir($cfg_basedir.$cfg_backup_dir."/".$filename)){
		 		echo "<tr><td><input name='files' value='$filename' type='checkbox' class='np'></td><td>$filename</td></tr>\r\n";
		 	}
		}
		?>
		</table>
		</td>
</tr>
</form>
<tr> 
<td bgcolor="#F1F2EC" colspan="2">&nbsp;</td>
</tr>
</table></td>
</tr>
</table>
</body>
</html>