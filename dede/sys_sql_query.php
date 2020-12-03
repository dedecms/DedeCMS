<?
require(dirname(__FILE__)."/config.php");
CheckPurview('sys_Data');

if(empty($dopost)) $dopost = "";
$dsql = new DedeSql(false);
if($dopost=="viewinfo") //查看表结构
{
	if(empty($tablename)) echo "没有指定表名！";
	else{
		$dsql->SetQuery("SHOW CREATE TABLE ".$dsql->dbName.".".$tablename);
    $dsql->Execute();
    $row2 = $dsql->GetArray();
    $ctinfo = $row2[1];
    echo "<xmp>".trim($ctinfo)."</xmp>";
	}
	$dsql->Close();
	exit();
}
else if($dopost=="opimize") //优化表
{
	if(empty($tablename)) echo "没有指定表名！";
	else{
	  $dsql->ExecuteNoneQuery("OPTIMIZE TABLE '$tablename'");
	  $dsql->Close();
	  echo "执行优化表： $tablename  OK！";
  }
	exit();
}
else if($dopost=="repair") //修复表
{
	if(empty($tablename)) echo "没有指定表名！";
	else{
	  $rs = $dsql->ExecuteNoneQuery("REPAIR TABLE '$tablename'");
	  $dsql->Close();
	  echo "修复表： $tablename  OK！";
	}
	exit();
}else if($dopost=="query") //执行SQL语句
{
	$sqlquery = trim(stripslashes($sqlquery));
	if(eregi("drop(.*)table",$sqlquery) 
	|| eregi("drop(.*)database",$sqlquery)){
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
	  		  if(ereg("[^0-9]",$k)){ echo "<font color='red'>{$k}：</font>{$v}<br/>\r\n"; }
	  	 }
	  }
	  exit();
	}
	if($querytype==2){
	   //普通的SQL语句
	   $sqlquery = str_replace("\r","",$sqlquery);
	   $sqls = split(";[ \t]{0,}\n",$sqlquery);
	   $nerrCode = ""; $i=0;
	   foreach($sqls as $q){
	     $q = trim($q); if($q==""){ continue; }
	     $dsql->ExecuteNoneQuery($q);
	     $errCode = trim($dsql->GetError());
	     if($errCode=="") $i++;
	     else $nerrCode .= "执行： <font color='blue'>$q</font> 出错，错误提示：<font color='red'>".$errCode."</font><br>";
     }
	   echo "成功执行{$i}个SQL语句！<br><br>";
	   echo $nerrCode;
  }else{
  	$dsql->ExecuteNoneQuery($sqlquery);
  	$nerrCode = trim($dsql->GetError());
  	echo "成功执行1个SQL语句！<br><br>";
	  echo $nerrCode;
	}
	$dsql->Close();
	exit();
}
?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=gb2312'>
<title>SQL命令行工具</title>
<link href='base.css' rel='stylesheet' type='text/css'>
</head>
<body background='img/allbg.gif' leftmargin='8' topmargin='8'>
<table width="98%" border="0" align="center" cellpadding="3" cellspacing="1" bgcolor="#666666">
<tr>
    <td height="19" background="img/tbg.gif"> 
      <table width="96%" border="0" cellspacing="1" cellpadding="1">
        <tr> 
          <td width="24%"><strong>SQL命令运行器：</strong></td>
          <td width="76%" align="right"> <b><a href="sys_data.php"><u>数据备份</u></a></b> 
            | <b><a href="sys_data_revert.php"><strong><u>数据还原</u></strong></a></b> 
          </td>
        </tr>
      </table></td>
</tr>
<tr>
    <td height="200" bgcolor="#FFFFFF" valign="top">
	<table width="100%" border="0" cellspacing="4" cellpadding="2">
        <form action="sys_sql_query.php" method="post" name="infoform" target="stafrm">
          <input type='hidden' name='dopost' value='viewinfo'>
          <tr bgcolor="#F3FBEC"> 
            <td width="15%" height="24" align="center">系统的表信息：</td>
            <td> 
              <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr> 
                  <td width="35%">
                  	<select name="tablename" id="tablename" style="width:100%" size="6">
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
                    </select></td>
                  <td width="2%">&nbsp;</td>
                  <td width="63%" valign="bottom">
                  	<table width="100%" border="0" cellspacing="0" cellpadding="0">
                      <tr> 
                        <td height="30">
<input type="Submit" name="Submit1" value="优化表" class="np" onClick="this.form.dopost.value='opimize';"></td>
                      </tr>
                      <tr> 
                        <td height="30">
<input type="Submit" name="Submit2" value="修复表" class="np" onClick="this.form.dopost.value='repair';"></td>
                      </tr>
                      <tr> 
                        <td height="30">
<input type="Submit" name="Submit3" value="查看表结构" class="np"></td>
                      </tr>
                    </table></td>
                </tr>
              </table></td>
          </tr>
          <tr> 
            <td height="200" align="center">返回信息：</td>
            <td>
			<iframe name="stafrm" frameborder="0" id="stafrm" width="100%" height="100%"></iframe>
			</td>
          </tr>
		  </form>
		  <form action="sys_sql_query.php" method="post" name="form1" target="stafrm">
          <input type='hidden' name='dopost' value='query'>
          <tr> 
            <td height="24" colspan="2" bgcolor="#F3FBEC"><strong>运行SQL命令行： 
              <input name="querytype" type="radio" class="np" value="0">
              单行命令（支持简单查询） 
              <input name="querytype" type="radio" class="np" value="2" checked>
              多行命令</strong></td>
          </tr>
		      <tr> 
            <td height="118" colspan="2">
			<textarea name="sqlquery" cols="60" rows="10" id="sqlquery" style="width:90%"></textarea> 
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