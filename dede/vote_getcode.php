<?
require_once(dirname(__FILE__)."/config.php");
require_once(dirname(__FILE__)."/../include/inc_vote.php");
$aid = ereg_replace("[^0-9]","",$aid);
?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=gb2312'>
<title>获取代码</title>
<link href='base.css' rel='stylesheet' type='text/css'>

</head>
<body background='img/allbg.gif' leftmargin='8' topmargin='8'>
<table width="98%" border="0" align="center" cellpadding="3" cellspacing="1" bgcolor="#666666">
  <tr>
    <td height="19" background="img/tbg.gif"><strong><a href="vote_main.php"><u>投票管理</u></a>&gt;&gt;获取代码</strong></td>
</tr>
<tr>
    <td height="200" bgcolor="#FFFFFF" valign="top"><table width="80%" border="0" cellspacing="2" cellpadding="2">
        <tr bgcolor="#EBF2DB"> 
          <td width="100%" bgcolor="#EBF2DB">在封面或主页模板中使用的标记：</td>
        </tr>
        <tr> 
          <td valign="top"> 
            <table width="600" border="0" cellspacing="0" cellpadding="0">
         <form name="form1" action="action_tag_test.php" target="stafrm" method="post">
         <input type="hidden" name="showsource" value="no">
			  <tr> 
                  <td width="370" height="153" align="center"> 
                    <textarea name="partcode" cols="45" rows="6" id="partcode">{dede:vote id='<?=$aid?>' lineheight='22'
tablewidth='100%' titlebgcolor='#EDEDE2'
titlebackground='' tablebgcolor='#FFFFFF'}
{/dede:vote}</textarea>
                  </td>
                <td width="230" rowspan="2">
                 <iframe name="stafrm" frameborder="0" id="stafrm" width="100%" height="100%"></iframe>
				</td>
              </tr>
              <tr>
                  <td height="53" align="center" valign="top"> 
                    <input type="submit" name="Submit" value="提交测试">
                  </td>
              </tr>
			  </form>
            </table> 
          </td>
        </tr>
        <tr bgcolor="#EBF2DB"> 
          <td bgcolor="#EBF2DB">默认生成的表单HTML：</td>
        </tr>
        <tr> 
          <td height="200" valign="top"> 
            <textarea name="htmlf" cols="60" rows="10" id="htmlf"><?
			$vt = new DedeVote($aid);
			echo $vt->GetVoteForm();
			$vt->Close();
			?></textarea>
          </td>
        </tr>
      </table> </td>
</tr>
</table>
</body>
</html>