<?
require("config.php");
$conn = connectMySql();
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>申请友情链接</title>
<link href="../base.css" rel="stylesheet" type="text/css">
<style type="text/css">
<!--
.style2 {
	color: #CC0000;
	font-size: 11pt;
}
-->
</style>
</head>
<body>
<table width="650" border="0" align="center" cellspacing="2">
  <tr> 
    <td><img src="img/recommend.gif" width="320" height="46"></td>
  </tr>
  <tr> 
    <td bgcolor="#CCCC99" height="6"></td>
  </tr>
  <tr> 
    <td bgcolor="#DEEFE2">&nbsp;申请友情链接：</td>
  </tr>
  <tr> 
    <td> <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr> 
          <td height="3"></td>
        </tr>
        <tr> 
          <td height="100" align="center" valign="top">
		  <form name="form1" method="post" action="flinkok.php">
		      <table width="98%"  border="0" cellspacing="1" cellpadding="3">
                <tr>
                  <td height="8" colspan="2"></td>
                </tr>
                <tr>
                  <td width="19%" height="25">网址：</td>
                  <td width="81%"><input name="url" type="text" id="url" value="http://" size="30"></td>
                </tr>
                <tr>
                  <td height="25">网站名称：</td>
                  <td><input name="fwebname" type="text" id="fwebname" size="30"></td>
                </tr>
                <tr>
                  <td height="25">网站Logo：</td>
                  <td><input name="logo" type="text" id="logo" size="30">
      (88*31 gif或jpg)</td>
                </tr>
                <tr>
                  <td height="25">网站简况：</td>
                  <td><textarea name="msg" cols="50" rows="4" id="msg"></textarea></td>
                </tr>
                <tr>
                  <td height="25">站长Email：</td>
                  <td><input name="email" type="text" id="email" size="30"></td>
                </tr>
                <tr>
                  <td height="25">网站类型：</td>
                  <td>
                    <select name="typeid" id="typeid">
                      <?
        $rs = mysql_query("select * from dede_flinktype",$conn);
        while($row=mysql_fetch_object($rs))
        {
        	echo "	<option value='".$row->ID."'>".$row->typename."</option>\r\n";
        }
        ?>
                    </select>
                  </td>
                </tr>
                <tr>
                  <td height="51">&nbsp;</td>
                  <td><input type="submit" name="Submit" value=" 提 交 ">
                      　 　
                      <input type="reset" name="Submit" value=" 重 置 "></td>
                </tr>
              </table>
		  </form>
			</td>
        </tr>
        <tr> 
          <td height="3"></td>
        </tr>
      </table></td>
  </tr>
  <tr> 
    <td bgcolor="#CCCC99" height="6"></td>
  </tr>
  <tr> 
    <td align="center"><a href="http://www.dedecms.com" target="_blank">Power 
      by DedeCms Dede织梦之旅</a></td>
  </tr>
</table>
	</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr> 
    <td>&nbsp;</td>
  </tr>
</table>
</body>
</html>
