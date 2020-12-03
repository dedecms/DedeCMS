<?
require("config.php");
$conn = connectMySql();
$query = "select dede_feedback.*,dede_art.title as arttitle from dede_feedback left join dede_art on dede_feedback.artID=dede_art.ID where dede_feedback.ID=$ID";
$rs = mysql_query($query,$conn);
$row = mysql_fetch_object($rs);
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>编辑评论</title>
<style type="text/css">
<!--
body {
	background-image: url(img/allbg.gif);
}
-->
</style>
<link href="base.css" rel="stylesheet" type="text/css">
</head>

<body>
&nbsp;
<table width="80%"  border="0" align="center" cellpadding="1" cellspacing="1" bgcolor="#666666">
  <tr>
    <td width="100%" height="24" colspan="2" background="img/tbg.gif"><strong><a href="<?=$_COOKIE["ENV_GOBACK_URL"]?>"><u>评论管理</u></a>&gt;&gt;编辑评论：</strong></td>
  </tr>
  <tr>
    <td height="187" colspan="2" align="center" bgcolor="#FFFFFF">
	<form name="form1" method="post" action="feedback_editok.php">
	<input type="hidden" name="ID" value="<?=$row->ID?>">
        <table width="98%" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td width="21%" height="24">评论所属文章：</td>
            <td width="79%"><?=$row->arttitle?></td>
          </tr>
          <tr> 
            <td height="24">评论人：</td>
            <td><?=$row->username?></td>
          </tr>
          <tr> 
            <td height="24">评论发布时间：</td>
            <td><?=$row->dtime?></td>
          </tr>
          <tr> 
            <td height="24">IP地址：</td>
            <td><?=$row->ip?></td>
          </tr>
          <tr> 
            <td height="24">评论内容：</td>
            <td>更改的评论内容HTML代码不会被屏蔽，可用HTML语法编辑。</td>
          </tr>
          <tr align="center"> 
            <td height="62" colspan="2">
			<textarea name="msg" cols="60" rows="5" id="msg"><?=$row->msg?></textarea> 
            </td>
          </tr>
          <tr> 
            <td height="24">管理员回复：</td>
            <td>回复内容的HTML代码会被屏蔽。</td>
          </tr>
          <tr align="center"> 
            <td height="24" colspan="2"><textarea name="adminmsg" cols="60" rows="5" id="adminmsg"></textarea></td>
          </tr>
          <tr align="center"> 
            <td height="40" colspan="2"> 
              <input type="submit" name="Submit" value="保存更改">
              　
              <input type="button" name="Submit2" value="不理返回" onClick="location='<?=$_COOKIE["ENV_GOBACK_URL"]?>';"></td>
          </tr>
        </table>
	  </form>
	  </td>
  </tr>
</table>
</body>
</html>