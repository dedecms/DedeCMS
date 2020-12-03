<?php
require_once(dirname(__FILE__)."/../../../common.inc.php");
?>
<HTML>
<HEAD>
<title>插入自定义内容</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<style>
.td{font-size:10pt;}
li,dd {
  list-style-type:none; margin:0px; padding:0px; 
}
ul,dl,ol,form,div {
  margin:0px; padding:0px;
}
</style>
<script src="common/fck_dialog_common.js" type="text/javascript"></script>
<script language=javascript>
var dialog = window.parent ;
var oEditor	= window.parent.InnerDialogLoaded() ;
var oDOM		= oEditor.FCK.EditorDocument ;
var FCK = oEditor.FCK;
window.onload = function()
{
	oEditor.FCKLanguageManager.TranslatePage(document) ;
	window.parent.SetOkButton( true ) ;
}
function Ok()
{
    var svalue = 0;
    if(!document.form1.selitems) {
    	return true;
    }
    if(document.form1.selitems.value) svalue = document.form1.selitems.value;
    else
    {
    	for(var i=0; i<document.form1.selitems.length; i++)
    	{
    		if(document.form1.selitems[i].checked) svalue = document.form1.selitems[i].value;
    	}
  	}
  	if(svalue > 0) oEditor.FCK.InsertHtml( document.getElementById('lab'+svalue).innerHTML );
    return true;
}

</script>
<link href="base.css" rel="stylesheet" type="text/css">
</HEAD>
<body bgcolor="#EBF6CD" topmargin="8">
  <form name="form1" id="form1">
  	<table border="0" width="98%" align="center">
    <tr> 
      <td>
      	请选择要插入内容：
      	<br>
      	<font color='#999999'>修改文件data/admin/mycode.php更改这些文字</font>
      </td>
    </tr>
    <tr height="50"> 
      <td style='line-height:160%' nowrap>
      	<ul>
      	<?php
      	$codefile = DEDEROOT."/data/admin/mycode.txt";
      	if(!file_exists($codefile))
      	{
      		 $testStr = "<"."?php
##测试HTML内容一
//#<div align=\"center\">这里是你要定义的HTML内容（前面的斜杠必须保留）</div>
##测试HTML内容二
//#<div align=\"center\">这里是你要定义的HTML内容（前面的斜杠必须保留）</div>";
           $fp = fopen($codefile, 'w');
           fwrite($fp, $testStr);
           fclose($fp);
      	}
      	$fp = fopen($codefile, 'r');
      	$str = '';
      	while( !feof($fp) )
      	{
      		$str .= trim(fgets($fp, 1024));
      	}
      	fclose($fp);
      	$strs = explode("##", $str);
      	$i = 0;
      	foreach($strs as $str)
      	{
      		if(!ereg('//#',$str)) continue;
      		$i++;
      		$tmds = explode("//#", $str);
      		echo "<li><input type='radio' name='selitems' value='$i'><b>{$tmds[0]}</b>\r\n";
					echo "<br /><label id='lab{$i}'>{$tmds[1]}</label>";
      		echo "</li>\r\n";
      	}
      	?>
      </ul>
      </td>
    </tr>
  </table>
  </form>
</body>
</HTML>

