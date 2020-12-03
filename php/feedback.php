<?
require("config.php");
if(empty($id)) $id="";
if(empty($ID)) $ID=$id;
$ID = ereg_replace("[^0-9]","",$ID);
if($ID=="") 
{
	echo "没输入ID号!";
	exit();
}
$conn = connectMySql();
$rs = mysql_query("Select dede_art.title,dede_art.dtime,dede_art.stime,dede_art.rank,dede_arttype.typedir From dede_art left join dede_arttype on dede_art.typeid=dede_arttype.ID where dede_art.ID=$ID",$conn);
$row = mysql_fetch_object($rs);
$title = $row->title;
$arturl = getFileName($row->stime,$ID,$row->typedir,$row->rank);
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>用户讨论</title>
<link href="../base.css" rel="stylesheet" type="text/css">
</head>
<body>
<table width="650" border="0" align="center" cellspacing="2">
  <tr> 
    <td><img src="img/feedback.gif" width="320" height="46"></td>
  </tr>
  <tr> 
    <td bgcolor="#CCCC99" height="6"></td>
  </tr>
  <tr>
    <td>最多显示前100条评论，点击此<a href="<?=$arturl?>"><u>返回查看原文：<?=$title?></u></a></td>
  </tr>
  <tr> 
    <td> 
      <?
    $rs = mysql_query("select * from dede_feedback where artID=$ID And ischeck=1 order by ID desc limit 0,100",$conn);
    while($row = mysql_fetch_object($rs))
    {	
    ?>
      <table width="100%" border="0" cellpadding="0" cellspacing="1" bgcolor="#CCCCCC">
        <tr bgcolor="#F7F7F7"> 
          <td width="23%">&nbsp;发言人： 
            <?=$row->username?>
          </td>
          <td width="43%"> &nbsp;IP地址： 
            <?=$row->ip?>
          </td>
          <td width="34%">&nbsp;时间： 
            <?=$row->dtime?>
          </td>
        </tr>
        <tr align="center" bgcolor="#FFFFFF"> 
          <td height="28" colspan="3"> <table width="98%" border="0" cellspacing="0" cellpadding="0">
              <tr> 
                <td>
                  <?=$row->msg?>
                </td>
              </tr>
            </table></td>
        </tr>
      </table>
      <table width="98%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td height="3"></td>
        </tr>
      </table>
      <?
	  }
	  ?>
    </td>
  </tr>
  <tr> 
    <td bgcolor="#CCCC99" height="6"></td>
  </tr>
  <tr> 
    <td> <table width="100%" border="0" cellspacing="2">
        <form action="sendfeedback.php" method="post" name="feedback">
          <input type="hidden" name="artID" value="<?=$id?>">
          <tr> 
            <td>用户名： 
              <input name="username" type="text" id="username" size="10" class="nb">
              （<a href="/member/reg.php" target="_blank"><u>新注册</u></a>） 密码： 
              <input name="pwd" type="text" id="pwd" size="10"  class="nb"> <input name="notuser[]" type="checkbox" id="notuser" value="1">
              匿名评论 <a href='javascript:if(document.feedback.msg.value!="") document.feedback.submit(); else alert("评论内容不能为空！");' class="coolbg" style="width:60">&nbsp;发表评论 
              </a></td>
          </tr>
          <tr> 
            <td>评论内容：(不能超过120字)<br>
              本系统会记录发贴者IP，请遵守互联网相关政策法规。</td>
          </tr>
          <tr> 
            <td><textarea name="msg" cols="70" rows="4" id="msg"></textarea></td>
          </tr>
        </form>
      </table></td>
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
