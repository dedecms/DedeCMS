<?
require(dirname(__FILE__)."/config.php");
SetPageRank(10);
if(empty($dopost)) $dopost = "";
$dsql = new DedeSql(false);
if($dopost=="viewinfo")
{
	if(empty($tablename)) echo "没有指定表名！";
	else
	{
		$dsql->SetQuery("SHOW CREATE TABLE ".$dsql->dbName.".".$tablename);
    $dsql->Execute();
    $row2 = $dsql->GetArray();
    $ctinfo = $row2[1];
    echo "<xmp>".trim($ctinfo)."</xmp>";
	}
	$dsql->Close();
	exit();
}
else if($dopost=="query")
{
	$sqlquery = trim(stripslashes($sqlquery));
	if(eregi("drop(.*)table",$sqlquery) || eregi("drop(.*)database",$sqlquery))
	{
		echo "<span style='font-size:10pt'>删除'数据表'或'数据库'的语句不允许在这里执行。</span>";
		$dsql->Close();
	  exit();
	}
	
	//运行查询语句
	if(eregi("^select ",$sqlquery))
	{
		$dsql->SetQuery($sqlquery);
	  $dsql->Execute();
	  if($dsql->GetTotalRow()<=0) echo "运行SQL：{$sqlquery}，无返回记录！";
	  else echo "运行SQL：{$sqlquery}，共有".$dsql->GetTotalRow()."条记录，最大返回100条！";
	  $j = 0;
	  while($row = $dsql->GetArray())
	  {
	  	$j++;
	  	if($j>100) break;
	  	echo "<hr size=1 width='100%'/>";
	  	echo "记录：$j";
	  	echo "<hr size=1 width='100%'/>";
	  	foreach($row as $k=>$v){
	  		if(ereg("[^0-9]",$k)){
	  			echo "<font color='red'>{$k}：</font>{$v}<br/>\r\n";
	  		}
	  	}
	  }
	  exit();
	}
	
	$sqls = explode(";",$sqlquery);
	$errCode = "";
	$i=0;
	foreach($sqls as $q)
	{
	  $q = trim($q);
	  if($q=="") continue;
	  $dsql->SetQuery($q);
	  $dsql->ExecuteNoneQuery();
	  $nerrCode = trim($dsql->GetError());
	  if($nerrCode=="") $i++;
	  else $errCode .= "执行： <font color='blue'>$q</font> 出错，错误提示：<font color='red'>".$nerrCode."</font><br>";
  }
	echo "成功执行{$i}个SQL语句！<br><br>";
	echo $errCode;
	$dsql->Close();
	exit();
}
?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=gb2312'>
<title>增加自定义页面</title>
<link href='base.css' rel='stylesheet' type='text/css'>
</head>
<body background='img/allbg.gif' leftmargin='8' topmargin='8'>
<table width="98%" border="0" align="center" cellpadding="3" cellspacing="1" bgcolor="#666666">
<tr>
    <td height="19" background="img/tbg.gif"> <strong>SQL命令运行器：</strong></td>
</tr>
<tr>
    <td height="200" bgcolor="#FFFFFF" valign="top">
	<table width="100%" border="0" cellspacing="4" cellpadding="2">
        <form action="sql_main.php" method="post" name="infoform" target="stafrm">
          <input type='hidden' name='dopost' value='viewinfo'>
          <tr bgcolor="#F3FBEC"> 
            <td width="15%" height="24" align="center">系统的表信息：</td>
            <td>
			<select name="tablename" id="tablename" style="width:250" size="6">
<?
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
?>
</select>
              &nbsp; <input type="Submit" name="Submit1" value="查看表信息" class="np">
			 </td>
          </tr>
          <tr> 
            <td height="200" align="center">返回信息：</td>
            <td>
			<iframe name="stafrm" frameborder="0" id="stafrm" width="100%" height="100%"></iframe>
			</td>
          </tr>
		  </form>
		  <form action="sql_main.php" method="post" name="form1" target="stafrm">
          <input type='hidden' name='dopost' value='query'>
          <tr> 
            <td height="24" colspan="2" bgcolor="#F3FBEC"><strong>运行SQL命令行：</strong></td>
          </tr>
		      <tr> 
            <td height="118" colspan="2">
			<textarea name="sqlquery" cols="60" rows="10" id="sqlquery" style="width:80%"></textarea> 
            </td>
          </tr>
          <tr> 
            <td height="53" align="center">&nbsp;</td>
            <td><input name="imageField" type="image" src="img/button_ok.gif" width="60" height="22" border="0"></td>
          </tr>
        </form>
      </table>
	 </td>
</tr>
</table>
<?
$dsql->Close();
?>
</body>
</html>