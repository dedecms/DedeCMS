<?
require("config.php");
if(empty($str)) $str="";
if(empty($activepath)) $activepath="";
if(empty($filename)) $filename="";
if($job == "save"){
    $file = "$base_dir$activepath/$filename";
    $str = str_replace("< textarea","<textarea",$str);
    $str = str_replace("< /textarea","</textarea",$str);
    $str = str_replace("< form","<form",$str);
    $str = str_replace("< /form","</form",$str);
    $str = stripslashes($str);
    $fp = fopen($file,"w");
    fputs($fp,$str);
    fclose($fp);
    Header("Location:file_view.php?activepath=$activepath");
    exit();
}
if($job == "edit"){
   $file = "$base_dir$activepath/$filename";
   $fp = fopen($file,"r");
   $str = fread($fp,filesize($file));
   fclose($fp);
   $str = eregi_replace("<textarea","< textarea",$str);
   $str = eregi_replace("</textarea","< /textarea",$str);
   $str = eregi_replace("<form","< form",$str);
   $str = eregi_replace("</form","< /form",$str);
}
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>文件编辑</title>
<style>
<!--
#fps0    {width:60}
#fps1    {color: #800000; background-color: #ffffcc}
td{ line-height: 18px; font-size: 10pt ;}
a:visited{ color: #000000; text-decoration: none }
a:link{ color: #000000; text-decoration: none; font-family: 宋体 }
a:hover{ color:red;background-color:yellow;}
-->
</style>
<script src="menu.js" language="JavaScript"></script>
<script language=javascript src=php.js></script>
<script>
function Post() {
	if (document.form1.filename.value==""){
		alert("文件名不能为空。");
		document.form1.filename.focus();
		return false;
        }
}	
</script>
</head>
<body bgcolor="#F2F4F3" leftmargin="15" topmargin="10">
<table width="98%" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr> 
    <td align="center"> 
      <table border=0 cellpadding=0 cellspacing=0 style="border-collapse: collapse" bordercolor=#111111 width=100%>
        <form method="POST" action="file_edit.php" name=form1 onSubmit="return Post()">
          <input type="hidden" name="job" value="save">
          <tr> 
            <td width=78% style="border-top-style: none; border-top-width: medium">
            <table border=0 cellpadding=0 cellspacing=0 style="border-collapse: collapse" width=100%>    <tr> 
                  <td>
				  <table border=0 cellpadding=0 cellspacing=0 style="border-collapse: collapse" width=496>
                      <tr> 
                        <td width=20%>工作目录：</td>
                        <td> &nbsp;&nbsp;&nbsp; <input name=activepath size=22 value="<? echo "$activepath"; ?>">
                          空白表示根目录 <b> </b></td>
                      </tr>
                      <tr> 
                        <td width=20%>文件名称：</td>
                        <td> &nbsp;&nbsp;&nbsp; <input name=filename size=22 value="<? echo "$filename"; ?>"> 
                          <a href=htm.htm target=_blank>网页处理功能</a></td>
                      </tr>
                    </table></td>
                </tr>
              </table></td>
          </tr>
          <tr> 
            <td width=78%> <textarea rows=18 name="str" cols=86 wrap="off"><? echo "$str"; ?></textarea> 
            </td>
          </tr>
          <tr> 
            <td width=78%> <p align=center><br>
                <input type=submit value="  保 存  " name=B1>
                &nbsp; 
                <input type=reset value="取消修改" name=B2>
                &nbsp; 
                <input type=button value=HTML预览 name=B3 onclick="view()">
                &nbsp; 
                <input type=button value="不理返回" name=B4 onclick="javascript:history.go(-1);">
                <br>
                　</td>
          </tr>
        </form>
      </table></td>
  </tr>
</table>
</body>

</html>
