<?
require("config.php");
$conn=connectMySql();
if(empty($errInfo)) $errInfo="";
if(empty($sql)) $sql="";
if($sql!="")
{
       $sql = trim(stripslashes($sql));
       mysql_query($sql,$conn);
	   if(mysql_errno()==0)
	   {
	          $errInfo = "成功执行指定的SQL指令！";
	   } 
	   else
	   {
	          $errInfo = mysql_error();  
	   }
}
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>执行mysql命令</title>
<link href="base.css" rel="stylesheet" type="text/css">
<script src="menu.js" language="JavaScript"></script>
<style>
.bt{border-left: 1px solid #FFFFFF; border-right: 1px solid #666666; border-top: 1px solid #FFFFFF; border-bottom: 1px solid #666666; background-color: #C0C0C0}
</style>
</head>
<body background="img/allbg.gif" leftmargin="6" topmargin="6">
<table width="100%" border="0" cellpadding="1" cellspacing="1" bgcolor="#666666">
  <form name="form1" action="sys_domysql.php" method="post">
    <tr align="center" bgcolor="#CCCCCC"> 
      <td height="26" background="img/tbg.gif"><strong>运行mysql命令[不用于查询]</strong></td>
    </tr>
    <tr> 
      <td height="20" bgcolor="#F1FAD6">提示信息：
        <?=$errInfo?>
      </td>
    </tr>
    <tr bgcolor="#FFFFFF"> 
      <td height="24">数据库资料表：<br> 
        <?
           $rs = mysql_list_tables($dbname,$conn);
           while($row = mysql_fetch_array($rs))
           {
	          	$rs2 = mysql_query("Select count(*) From ".$row[0],$conn);
	          	$row2 = mysql_fetch_array($rs2);
	          	$dd = $row2[0];
	          	$ecline="&nbsp;<span style='font-size:10pt;color:#800040'>@<b>".$row[0]." </b>($dd)</span> <a  href='#' onClick=\"javascript:document.form1.sql.value+='OPTIMIZE TABLE ".$row[0].";\\n';\">[优化]</a> <a href='#' onClick=\"javascript:document.form1.sql.value+='UPDATE ".$row[0]." SET = WHERE ;\\n';\">[更新]</a> <a  href='#' onClick=\"javascript:document.form1.sql.value+='Delete From ".$row[0]." WHERE ;\\n';\">[删除行]</a><br>\n";
            	echo $ecline;
            	$fns = "";
            	$fd = mysql_list_fields($dbname,$row[0],$conn);
            	for($i=0;;$i++)
            	{
            		$fn = @mysql_field_name($fd, $i);
            		if($fn=="") break;
            		else $fns .= $fn.", ";
            	}
            	echo "&nbsp;<font color='#555555'>(列信息: ".ereg_replace(", $","",$fns).")</font><br>\n";
            }
			?>
      </td>
    </tr>
    <tr bgcolor="#FFFFFF"> 
      <td height="119" align="center"> <textarea name="sql" cols="70" rows="6" id="sql"><?=$sql?></textarea> 
        &nbsp; &nbsp; </td>
    </tr>
    <tr align="center" bgcolor="#CCCCCC"> 
      <td height="28" background="img/tbgv.gif"> 
        <input type="button" name="Submit" value=" 确 认 " onclick="document.form1.submit();" class="bt"> 
        <a name="sqlform"/>
      </td>
    </tr>
  </form>
</table>
</body>

</html>
