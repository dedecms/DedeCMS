<?
require("config.php");
$conn=connectMySql();
if(empty($job)) $job="";
if($job=="ok")
{
      $ID = ereg_replace("[^0-9]","",$ID);
	  mysql_query("Update dede_member set rank=$rank,isup=0 where ID=".$ID,$conn);
      echo "<script>alert('成功审核一个用户！');location.href='".$_COOKIE["ENV_GOBACK_URL"]."';</script>";
      exit();
}
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>设定用户的级别</title>
<link href="base.css" rel="stylesheet" type="text/css">
<script src="menu.js" language="JavaScript"></script>
<style>
.bt{border-left: 1px solid #FFFFFF; border-right: 1px solid #666666; border-top: 1px solid #FFFFFF; border-bottom: 1px solid #666666; background-color: #C0C0C0}
</style>
</head>
<body background="img/allbg.gif" leftmargin="8" topmargin="8">
<table width="300" border="0" align="center" cellpadding="1" cellspacing="1" bgcolor="#666666">
  <form name="form1" action="user_check.php" method="post">
    <input type="hidden" name="ID" value="<? echo $ID ?>">
    <input type="hidden" name="job" value="ok">
    <tr align="center"> 
      <td height="26" colspan="2" background='img/tbg.gif'><strong>确认设定用户的级别</strong></td>
    </tr>
    <tr bgcolor="#FFFFFF"> 
      <td height="50" colspan="2" align="center">用户级别： 
        <select name="rank" id="rank">
          <?
                      $rs = mysql_query("Select * From dede_membertype where rank>=0 And rank!=1",$conn);
                      while($row=mysql_fetch_object($rs))
                      {
                      	echo "<option value=\"".$row->rank."\">".$row->membername."</option>\n";
                      }
                      ?>
        </select></td>
    </tr>
    <tr align="center" bgcolor="#EFEFEF"> 
      <td height="28" colspan="2"> <input type="button" name="Submit" value=" 确 认 " onclick="document.form1.submit();" class="bt"> 
        &nbsp;&nbsp; <input type="button" name="Submit2" value=" 取 消 " onclick="history.go(-1);" class="bt"></td>
    </tr>
  </form>
</table>
</body>

</html>
