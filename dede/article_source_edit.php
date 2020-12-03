<?php 
require_once(dirname(__FILE__)."/config.php");
CheckPurview('sys_Source');
if(empty($dopost)) $dopost = "";
if(empty($allsource)) $allsource = "";
else $allsource = stripslashes($allsource);
$m_file = dirname(__FILE__)."/inc/source.txt";
//保存
if($dopost=="save")
{
   $fp = fopen($m_file,'w');
   flock($fp,3);
   fwrite($fp,$allsource);
   fclose($fp);
   echo "<script>alert('Save OK!');</script>";
}
//读出
if(empty($allsource)&&filesize($m_file)>0){
   $fp = fopen($m_file,'r');
   $allsource = fread($fp,filesize($m_file));
   fclose($fp);
}
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>文章来源管理</title>
<link href="base.css" rel="stylesheet" type="text/css">
</head>
<body background='img/allbg.gif' leftmargin='8' topmargin='8'>
<table width="98%" border="0" cellpadding="3" cellspacing="1" bgcolor="#98CAEF" align="center">
  <form name="form1" action="article_source_edit.php" method="post">
    <input type="hidden" name="dopost" value="save">
    <tr> 
      <td width="968" height="20" colspan="2" background='img/tbg.gif'> <table width="98%" border="0" cellpadding="0" cellspacing="0">
          <tr> 
            <td width="30%" height="18"><strong>文章来源管理：</strong></td>
            <td width="70%" align="right">&nbsp;</td>
          </tr>
        </table></td>
    </tr>
    <tr> 
      <td colspan="2" valign="top" bgcolor="#FFFFFF"> 每行保存一个来源，更改结果后需重载档案发布页面。</td>
    </tr>
    <tr> 
      <td height="62" colspan="2" bgcolor="#FFFFFF"> <textarea name="allsource" id="allsource" style="width:100%;height:300"><?php echo $allsource?></textarea> 
      </td>
    </tr>
    <tr> 
      <td height="31" colspan="2" bgcolor="#F8FBFB" align="center"> <input type="submit" name="Submit" value="保存数据"> 
      </td>
    </tr>
  </form>
</table>
</body>
</html>
