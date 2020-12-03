<?php 
require_once(dirname(__FILE__)."/config.php");
CheckPurview('sys_ArcBatch');
if($_POST){
	$isMagic = @ini_get("magic_quotes_gpc");
	if($isMagic) foreach($_POST AS $key => $value) $$key = stripslashes($value);
	else foreach($_POST AS $key => $value) $$key = $value;
	if($reggo==0){
	   $rs = preg_replace("/$testrule/$testmode",$rpvalue,$testtext);
	   echo "<xmp>[".$rs."]</xmp>";
  }else{
  	 $backarr = array();
  	 preg_match_all("/$testrule/$testmode",$testtext,$backarr);
  	 echo "<xmp>";
  	 foreach($backarr as $k=>$v){
  	 	  echo "$k";
  	 	  if(!is_array($v)) echo " - $v \r\n";
  	 	  else{
  	 	  	 echo " Array \r\n";
  	 	  	 foreach($v as $kk=>$vv){ echo "----$kk - $vv \r\n"; }
  	 	  }
  	 }
  	 echo "</xmp>";
  }
	exit();
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>规则测试器</title>
<link href='base.css' rel='stylesheet' type='text/css'>
</head>
<body leftmargin="0" topmargin="6">
<table width="98%" border="0" align="center" cellpadding="6" cellspacing="1" bgcolor="#B7C1A6">
  <tr bgcolor="#EDEEE1"> 
    <td height="30" colspan="2"><strong>正则规则测试器：</strong></td>
  </tr>
  <form action="ruletest.php" method="post" name="form1" target="stafrm">
  <input type="hidden" name="action" value="go">
  <tr bgcolor="#FFFFFF"> 
      <td width="23%" height="110">输入内容：</td>
    <td width="77%"><textarea name="testtext" id="testtext" style="width:98%;height:90px"></textarea></td>
  </tr>
  <tr bgcolor="#FFFFFF">
      <td height="30">测试规则：(不需加/)</td>
    <td><input name="testrule" type="text" id="testrule" style="width:98%"></td>
  </tr>
  <tr bgcolor="#FFFFFF"> 
    <td height="30">匹配模式：</td>
    <td><input name="testmode" type="text" id="testmode" value="isU">
        <input name="reggo" type="radio" value="0" checked>
        替换为 
        <input name="rpvalue" type="text" id="rpvalue" size="10"> 
        <input type="radio" name="reggo" value="1">
        返回匹配结果</td>
  </tr>
  <tr align="center" bgcolor="#FFFFFF"> 
    <td height="35" colspan="2"> <table width="80%" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td width="23%" align="right"> 
              <input type="submit" name="Submit" value="提交测试" class='coolbg'>
            </td>
            <td width="77%">&nbsp;</td>
          </tr>
        </table></td>
  </tr>
  </form>
  <tr bgcolor="#EDEEE1"> 
    <td colspan="2"> 
      <table width="100%" cellpadding="0" cellspacing="0">
        <tr> 
          <td width="74%"><strong>结果： </strong></td>
          <td width="26%" align="right"> <script language='javascript'>
            	function ResizeDiv(obj,ty)
            	{
            		if(ty=="+") document.all[obj].style.pixelHeight += 50;
            		else if(document.all[obj].style.pixelHeight>80) document.all[obj].style.pixelHeight = document.all[obj].style.pixelHeight - 50;
            	}
            	</script>
            [<a href='#' onClick="ResizeDiv('mdv','+');">增大</a>] [<a href='#' onClick="ResizeDiv('mdv','-');">缩小</a>] 
          </td>
        </tr>
      </table></td>
  </tr>
  <tr align="center" valign="top" bgcolor="#FFFFFF"> 
    <td colspan="2"> 
      <div id='mdv' style='width:100%;height:100;'> 
        <iframe name="stafrm" frameborder="0" id="stafrm" width="100%" height="100%"></iframe>
      </div>
      <script language="JavaScript">
	  document.all.mdv.style.pixelHeight = screen.height - 450;
	  </script>
    </td>
  </tr>
</table>
</body>
</html>
