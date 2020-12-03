<?
require("config.php");
if(empty($artID)){ echo "文章ID为空!";exit();}
$ID = ereg_replace("[^0-9]","",$artID);
$conn = connectMySql();
$rs = mysql_query("Select dede_art.title,dede_art.dtime,dede_art.stime,dede_art.rank,dede_arttype.typedir From dede_art left join dede_arttype on dede_art.typeid=dede_arttype.ID where dede_art.ID=$ID",$conn);
$row = mysql_fetch_object($rs);
$title = $row->title;
$arturl = GetFileName($row->stime,$ID,$row->typedir,$row->rank);
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>推荐好友</title>
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
    <td height="28"><span class="style2">&nbsp;文章名称： <a href="<?=$arturl?>"><?=$title?></a></span></td>
  </tr>
  <tr> 
    <td bgcolor="#DFEAE4">&nbsp;我要把它发送给我的好友：</td>
  </tr>
  <tr> 
    <td> <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr> 
          <td height="3"></td>
        </tr>
        <tr> 
          <td height="100" align="center" valign="top">
		  <form name="form1" method="post" action="recommandok.php">
		  <input type="hidden" name="arturl" value="<?=$arturl?>">
		      <table width="98%" border="0" cellspacing="0" cellpadding="0">
                <tr> 
                  <td width="19%" height="30">你好友的Email：</td>
                  <td width="81%"> <input name="email" type="text" id="email"> 
                  </td>
                </tr>
                <tr> 
                  <td height="30">你的留言：</td>
                  <td>&nbsp;</td>
                </tr>
                <tr align="center"> 
                  <td height="61" colspan="2">
				  <textarea name="msg" cols="72" rows="6" id="msg">你好，我在 [<?=$webname?>] 发现了一篇很好的文章：
你不妨去看看吧！
文章的名称是：<?=$title?> 网址是：<?=$base_url.$arturl?>
</textarea></td>
                </tr>
                <tr> 
                  <td height="50">&nbsp;</td>
                  <td><input type="submit" name="Submit" value=" 发 送 "></td>
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
