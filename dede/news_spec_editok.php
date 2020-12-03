<?
require("config.php");
require("inc_makespec.php");
$conn = connectMySql();
$usql_1 = "Update dede_spec set
    typeid='$typeid',
    spectitle='$spectitle',
    specimg='$specimg',
    imgtitle='$imgtitle',
    imglink='$imglink',
    specmsg='$specmsg',
    specartid='$specartid',
    speclikeid='$speclikeid'
where ID=$ID";
$usql_2 = "Update dede_art set
    title='$spectitle',
    typeid='$typeid',
    msg='".cn_substr($specmsg,240)."'
where spec=$ID";
mysql_query($usql_1,$conn);
mysql_query($usql_2,$conn);
$mk = new MakeSpec($ID);
$makeok = $mk->MakeMode();
?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=gb2312'>
<title>更改专题</title>
<link href='base.css' rel='stylesheet' type='text/css'>
<script src='menu.js' language='JavaScript'></script>
</head>
<body background='img/allbg.gif' leftmargin='6' topmargin='16'>
<table width="80%" border="0" cellpadding="3" cellspacing="1" bgcolor="#666666" align="center">
  <tr> 
    <td height="19" background="img/tbg.gif">成功更改一个专题&nbsp;&nbsp;[<a href="list_news_spec.php">专题管理</a>] [<a href="add_news_spec.php">专题向导</a>]</td>
  </tr>
  <tr> 
    <td height="95" align="center" bgcolor="#FFFFFF"> <table width="80%" border="0" cellspacing="0" cellpadding="0">
        <tr> 
            <td colspan="2">&nbsp;</td>
          </tr>
          <tr> 
            <td colspan="2" height="50">
             成功更改一个专题，查看：<?=$makeok?>
            </td>
          </tr>
          <tr> 
            <td height="20" colspan="2">&nbsp;</td>
          </tr>
        </form>
      </table></td>
  </tr>
</table>
</body>

</html>