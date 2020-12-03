<?php 
require_once(dirname(__FILE__)."/../config_base.php");
if(empty($height)){ $height=250; }
if(empty($edittype)) $edittype="html";
if(empty($modetype)) $modetype=0;
if($modetype=="full"||$modetype==""||$modetype=="basic"){
	$trheight = $height - 30;
	$height = $height - 86;
}else{
	$trheight = $height - 30;
	$height = $height - 52;
}
if($edittype=="html") $isediter=1;
else $isediter=0;
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>DedeCms HtmlEditer</title>
<script type="text/JavaScript" language="JavaScript">
var _MyEDoc;
var parentField	= window.parent.document.getElementsByName('<?php echo $InstanceName?>')[0];
var isediter=<?php echo $isediter?>;
var parentForm = parentField.form;
</script>
<script language="JavaScript" type="text/JavaScript" src="js/main.js?n"></script>
<link href="base.css" rel="stylesheet" type="text/css">
<style>
	.himg{ cursor:hand }
	.nom { background-color:#6699FF; width:24px; }
	.msd { background-color:#3366FF; width:24px; }
	.msm { background-color:#99CCFF; width:24px; }
</style>
</head>
<body bgcolor="#FAFCF1" topmargin="0" leftmargin="0">
<table width="100%" height="100%" cellspacing="0" cellpadding="0">
  <form name="form1" method="post" action="">
  <input type="hidden" name="ishtml" value="1">
    </tr>
    <tr id="menubar" style="display: block"> 
      <td width="100%"> 
        <?php 
        if($modetype=="small") echo "<script src='js/smallbar.js'></script>";
        else if(eregi("member",$modetype)) echo "<script src='js/memberbar.js'></script>";
        else echo "<script src='js/fullbar.js'></script>";
        ?>
      </td>
    </tr>
    <tr> 
      <td height="2"></td>
    </tr>
    <tr id="htmledit" style="display:block" height='<?php echo $height?>'>
      <td height='<?php echo $height?>'> 
          <iframe name="_MyEditor" id="_MyEditor" height="100%" width="100%" onLoad="GetParentValue()" scrolling="yes"></iframe>
      </td>
    </tr>
    <tr id="textedit" style="display:none" height="<?php echo $trheight?>"> 
      <td height="<?php echo $trheight?>"> 
          <textarea name="artbody" id="artbody" style="width:100%;height:100%;"></textarea>
      </td>
    </tr>
    <tr> 
      <td> 
      <table>
      <tr>
         <td width="25"><input name="modeCheck" type="radio" onClick="Show_MyEditor();" value="1" checked></td>
            <td width="49">可视化</td>
            <td width="28"><input type="radio" name="modeCheck" value="0" onClick="ShowCode_MyEditor();"></td>
            <td width="62">编辑源码</td>
            <td></td>
        </tr>
        </table>
      </td>
    </tr>
  </form>
 <script>Load_MyEditor()</script>
</table>
</body>
</html>