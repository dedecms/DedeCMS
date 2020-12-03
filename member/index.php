<?
require_once(dirname(__FILE__)."/config.php");
CheckRank(0,0);
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>会员主页</title>
<link href="base.css" rel="stylesheet" type="text/css">	
</head>
<body leftmargin="0" topmargin="0">
<table width="760" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr bgcolor="#FFFFFF"> 
    <td height="50" colspan="3"><img src="img/member.gif" width="320" height="46"></td>
  </tr>
  <tr> 
    <td width="17" rowspan="2" bordercolor="#FFFFFF" bgcolor="#808DB5">&nbsp;</td>
    <td width="168" bordercolor="#FFFFFF" bgcolor="#808DB5">&nbsp;</td>
    <td width="575" align="right"><a href="<?=$cfg_indexurl?>"><u>网站主页</u></a>&nbsp; <a href="index_do.php?fmdo=login&dopost=exit"><u>退出登录</u></a></td>
  </tr>
  <tr> 
    <td colspan="2" valign="top"> <table width="100%" height="200" border="0" cellpadding="1" cellspacing="1" bgcolor="#000000">
        <tr> 
          <td height="100" align="center" valign="top" bgcolor="#FFFFFF"> <table width="98%" border="0" cellspacing="0" cellpadding="0">
              <tr> 
                <td height="6" colspan="2"></td>
              </tr>
              <tr> 
                <td height="40" colspan="2"><font color="red"> 
                  <?=$cfg_ml->M_UserName?>
                  </font> 你好，欢迎登录到会员管理中心</td>
              </tr>
              <tr> 
                <td height="18" colspan="2"> 
                  <?=$cfg_ml->GetSta()?>
                </td>
              </tr>
              <tr> 
                <td colspan="2" align="center"><hr size="1"></td>
              </tr>
              <tr> 
                <td width="15%" height="40" align="center">管理菜单：</td>
                <td width="85%"> <a href="mystow.php"><u>我的收藏夹</u></a>&nbsp; <a href="mypay.php"><u>消费记录</u></a>&nbsp; 
                  <a href="artsend.php"><u>投稿</u></a>&nbsp; <a href="artlist.php"><u>管理稿件</u></a>&nbsp; 
                  <a href="edit_info.php"><u>更改个人资料</u></a> </td>
              </tr>
              <tr bgcolor="#F8F8F5"> 
                <td height="22" colspan="2" align="center">&nbsp;</td>
              </tr>
              <tr> 
                <td height="40" align="center">我要升级：</td>
				<form name="formrank" action="index_do.php" method="post">
                  <input type="hidden" name="fmdo" value="user">
				  <input type="hidden" name="dopost" value="uprank">
                <td height="40"> 
                  <?
    $dsql = new DedeSql(false);
    $row = $dsql->GetOne("Select rank From #@__arcrank order by rank desc limit 0,1");
    $maxrank = $row['rank'];
    if($cfg_ml->M_Type >= $maxrank){
    echo "你已经是最高级别的会员，不能再升级。";
    }
    else
    {
       echo "<select name=\"uptype\" id=\"uptype\">\r\n";
       $dsql->SetQuery("Select membername,rank From #@__arcrank where rank>'".$cfg_ml->M_Type."'");
       $dsql->Execute();
       while($row = $dsql->GetObject())
       {
        echo "<option value='".$row->rank."'>".$row->membername."</option>";
       }
       echo "</select>\r\n";
       echo "<input type=\"submit\" name=\"Submit\" value=\"提交\">\r\n";
   }
   $dsql->Close();
?>
                </td>
                </form>
              </tr>
              <tr> 
                <td height="40" align="center" bgcolor="#F8F8F5">我要充值：</td>
                <td height="40" bgcolor="#F8F8F5"> 
                  <table width="426" border="0" cellspacing="0" cellpadding="0">
                <form name="formrank" action="index_do.php" method="post">
                  <input type="hidden" name="fmdo" value="user">
				  <input type="hidden" name="dopost" value="addmoney">
					<tr> 
                      <td width="62">金币数量：</td>
                      <td width="77" align="center"> <input name="money" type="text" id="money" value="500" size="6"> 
                      </td>
                      <td width="56">验证码：</td>
                      <td width="82" align="center">
					  <input name="vdcode" type="text" id="vdcode" size="8"> 
                      </td>
                      <td width="61"><img src='../include/validateimg.php' width='50' height='20'></td>
                      <td width="88"><input type="submit" name="Submit" value="提交"></td>
                    </tr>
					</form>
                  </table>
                </td>
              </tr>
              <tr> 
                <td height="20" colspan="2" align="center">&nbsp; </td>
              </tr>
            </table></td>
        </tr>
      </table></td>
  </tr>
</table>
<p align='center'>
<?=$cfg_powerby?>
</p>
</body>
</html>
