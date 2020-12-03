<?
require_once(dirname(__FILE__)."/config.php");
CheckPurview('sys_Writer');
if(empty($dopost)) $dopost = "";
if(empty($allwriter)) $allwriter = "";
else $allwriter = stripslashes($allwriter);
$m_file = dirname(__FILE__)."/inc/writer.txt";
//保存
if($dopost=="save")
{
   $fp = fopen($m_file,'w');
   flock($fp,3);
   fwrite($fp,$allwriter);
   fclose($fp);
   echo "<script>alert('Save OK!');</script>";
}
//读出
if(empty($allwriter)&&filesize($m_file)>0){
   $fp = fopen($m_file,'r');
   $allwriter = fread($fp,filesize($m_file));
   fclose($fp);
}
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>文章作者管理</title>
<link href="base.css" rel="stylesheet" type="text/css">
</head>
<body background='img/allbg.gif' leftmargin='8' topmargin='8'>
<table width="98%" border="0" cellpadding="3" cellspacing="1" bgcolor="#666666" align="center">
  <form name="form1" action="article_writer_edit.php" method="post">
    <input type="hidden" name="dopost" value="save">
    <tr> 
      <td width="968" height="20" colspan="2" background='img/tbg.gif'> <table width="98%" border="0" cellpadding="0" cellspacing="0">
          <tr> 
            <td width="30%" height="18"><strong>文章作者管理：</strong></td>
            <td width="70%" align="right">&nbsp;</td>
          </tr>
        </table></td>
    </tr>
    <tr> 
      <td colspan="2" valign="top" bgcolor="#FFFFFF"> 把作者姓名用半角逗号“,”分开，更改结果后需重载档案发布页面。</td>
    </tr>
    <tr> 
      <td height="62" colspan="2" bgcolor="#FFFFFF"> <textarea name="allwriter" id="allwriter" style="width:100%;height:300"><?=$allwriter?></textarea> 
      </td>
    </tr>
    <tr> 
      <td height="31" colspan="2" bgcolor="#FAFAF1" align="center"> <input type="submit" name="Submit" value="保存数据"> 
      </td>
    </tr>
  </form>
</table>
</body>
</html>
