<?
require("config.php");
?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=gb2312'>
<title>投票管理</title>
<link href='base.css' rel='stylesheet' type='text/css'>
<script language="javascript">
     var i=1;
     function AddItem()
     { 
        i++;
		if(i>9)
		{
			alert("最大只允许9个选项！");
			return;
		}
        document.all.voteitem.innerHTML+="<br>选项"+i+"： <input name='voteitem"+i+"' type='text' size='30'>";
     }
	function ResetItem()
    { 
        i = 1;
		document.all.voteitem.innerHTML="选项1： <input name='voteitem1' type='text' size='30'>";
    }
</script>

</head>
<body background='img/allbg.gif' leftmargin='8' topmargin='8'>
<table width="98%" border="0" align="center" cellpadding="3" cellspacing="1" bgcolor="#666666">
  <tr>
    <td height="19" background="img/tbg.gif"><b>投票管理</b>&gt;&gt;增加投票&nbsp;&nbsp;[<a href="add_vote.php"><u>管理以往投票内容记录</u></a>]</td>
</tr>
<tr>
    <td height="200" bgcolor="#FFFFFF" valign="top">
	<form name="form1" method="post" action="add_voteok.php">
	    <table width="100%" border="0" cellspacing="4" cellpadding="4">
          <tr> 
            <td width="12%">投票名称：</td>
            <td width="88%"> <input name="votename" type="text" id="votename">
              （不要存在\ / : ? * &quot; &lt;&gt; | 符号） </td>
          </tr>
          <tr> 
            <td>投票选项：</td>
            <td><input type="button" value="增加投票选项" name="bbb" class="bt1" onClick="AddItem();">
              　
              <input type="button" value="重置投票选项" name="bbb2" class="bt1" onClick="ResetItem();"></td>
          </tr>
          <tr> 
            <td colspan="2">
			<div id="voteitem">
			选项1： 
                <input name="voteitem1" type="text" id="voteitem1" size="30">
			 </div>
			 </td>
          </tr>
          <tr> 
            <td>&nbsp;</td>
            <td><input type="submit" name="Submit" value="生成投票数据"></td>
          </tr>
          <tr> 
            <td colspan="2">&nbsp;</td>
          </tr>
        </table>
	  </form>
	  </td>
</tr>
</table>
</body>
</html>