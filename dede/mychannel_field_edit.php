<?php 
require_once(dirname(__FILE__)."/config.php");
if(empty($action)) $action = '';

//模型信息
$dsql = new DedeSql(false);
$mysql_version = $dsql->GetVersion();
$mysql_versions = explode(".",trim($mysql_version));
$mysql_version = $mysql_versions[0].".".$mysql_versions[1];
$row = $dsql->GetOne("Select fieldset,addtable,issystem From #@__channeltype where ID='$ID'");
$fieldset = $row['fieldset'];
require_once(dirname(__FILE__)."/../include/pub_dedetag.php");
$dtp = new DedeTagParse();
$dtp->SetNameSpace("field","<",">");
$dtp->LoadSource($fieldset);
foreach($dtp->CTags as $ctag){
	if($ctag->GetName()==$fname) break;
}
$ds = file(dirname(__FILE__)."/inc/fieldtype.txt");
foreach($ds as $d){
   $dds = explode(',',trim($d));
   $fieldtypes[$dds[0]] = $dds[1];
}

//保存更改
/*
function _SAVE()
*/
//-----------
if($action=='save'){
	if($row['issystem']==1){
		 $dsql->Close();
		 ShowMsg("对不起，你在查看的是系统模型字段，不允许更改！","-1");
		 exit();
	}
	$trueTable = $row['addtable'];
  //检测数据库是否存在附加表，不存在则新建一个
  if(!$dsql->IsTable($trueTable)){
     $tabsql = "CREATE TABLE IF NOT EXISTS `$trueTable`(
	           `aid` int(11) NOT NULL default '0',
             `typeid` int(11) NOT NULL default '0',
     ";
	   if($mysql_version < 4.1){
        $tabsql .= "    PRIMARY KEY  (`aid`), KEY `".$trueTable."_index` (`typeid`)\r\n) TYPE=MyISAM; ";
     }else{
        $tabsql .= "    PRIMARY KEY  (`aid`), KEY `".$trueTable."_index` (`typeid`)\r\n) ENGINE=MyISAM DEFAULT CHARSET=".$cfg_db_language."; ";
     }
     $dsql->ExecuteNoneQuery($tabsql);
  }
  //检测附加表里含有的字段
	$fields = Array();
	$rs = mysql_query("show fields from $trueTable",$dsql->linkID);
	while($row = mysql_fetch_array($rs)){
		$fields[$row['Field']] = $row['Type'];
	}
	//修改字段配置信息
	$dfvalue = $vdefault;
	$isnull = ($isnull==1 ? "true" : "false");
	$mxlen = $maxlength;
	$fieldname = $fname;
	//检测被修改的字段类型，并更新数据表
	if($dtype=="int"||$dtype=="datetime"){
    	if($dfvalue=="" || ereg("[^0-9-]",$dfvalue)){ $dfvalue = 0; }
    	$tabsql = " `$fieldname` int(11) NOT NULL default '$dfvalue';";
    	$buideType = "int(11)";
  }else if($dtype=="float"){
      if($dfvalue=="" || ereg("[^0-9\.-]",$dfvalue)){ $dfvalue = 0; }
      $tabsql = " `$fieldname` float NOT NULL default '$dfvalue';";
    	$buideType = "float";
  }else if($dtype=="img"||$dtype=="media"||$dtype=="addon"){
    	if($mxlen=="") $mxlen = 200;
    	if($mxlen > 255) $mxlen = 50;
    	$tabsql = " `$fieldname` varchar($mxlen) NOT NULL default '$dfvalue';";
    	$buideType = "varchar($mxlen)";
  }else if($dtype=="multitext"||$dtype=="htmltext"){
    	$tabsql = " `$fieldname` mediumtext;";
    	$buideType = "mediumtext";
  }else if($dtype=="textdata"){
    	$tabsql = " `$fieldname` varchar(100) NOT NULL default '';";
    	$buideType = "varchar(100)";
  }else{
    	if($mxlen=="") $mxlen = 50;
    	if($mxlen > 255) $mxlen = 250;
    	$tabsql = " `$fieldname` varchar($mxlen) NOT NULL default '$dfvalue';";
    	$buideType = "varchar($mxlen)";
  }
	//检测旧数据类型，并替换为新配置
	foreach($dtp->CTags as $tagid=>$ctag){
		if($ctag->GetName()==$fieldname){
      $dfvalue = $ctag->GetAtt('default');
      $isnull = $ctag->GetAtt('isnull');
      $dtype = $ctag->GetAtt('type');
      $mxlen = $ctag->GetAtt('maxlength');
      if(isset($fields[$fieldname]) && $fields[$fieldname]!=$buideType){
     	  $tabsql = "ALTER TABLE `$trueTable` CHANGE `$fieldname` ".$tabsql;
      }else if(!isset($fields[$fieldname])){
     	  $tabsql = "ALTER TABLE `$trueTable` ADD ".$tabsql;
      }else{
     	  $tabsql = "";
      }
      if($tabsql!=""){
      	echo $tabsql;
      	exit();
     	  $dsql->ExecuteNoneQuery($tabsql);
      }
			$dtp->Assign($tagid,stripslashes($editfield));
		}
	}
	$oksetting = addslashes($dtp->GetResultNP());
	$dsql->ExecuteNoneQuery("Update #@__channeltype set fieldset='$oksetting' where ID='$ID' ");
	$dsql->Close();
	ShowMsg("成功更改一个字段的配置！","mychannel_field.php?ID={$ID}&dopost=edit");
	exit();
}
/*
删除字段
function _DELETE()
*/
else if($action=="delete"){
	if($row['issystem']==1){
		 $dsql->Close();
		 ShowMsg("对不起，你在查看的是系统模型，不允许更改！","-1");
		 exit();
	}
	$trueTable = $row['addtable'];
	//检测旧数据类型，并替换为新配置
	foreach($dtp->CTags as $tagid=>$ctag){
		if($ctag->GetName()==$fname){
			$dtp->Assign($tagid,'#@Delete@#');
		}
	}
	$oksetting = addslashes($dtp->GetResultNP());
	$dsql->ExecuteNoneQuery("Update #@__channeltype set fieldset='$oksetting' where ID='$ID' ");
	$dsql->ExecuteNoneQuery("ALTER TABLE `$trueTable` DROP `$fname` ");
	$dsql->Close();
	ShowMsg("成功删除一个字段！","mychannel_field.php?ID={$ID}&dopost=edit");
	exit();
}
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>更改字段</title>
<style type="text/css">
<!--
body {
	background-image: url(img/allbg.gif);
}
-->
</style>
<link href="base.css" rel="stylesheet" type="text/css">
<script language="javascript">
var notAllow = " aid ID typeid typeid2 sortrank iscommend ismake channel arcrank click money title shorttitle color writer source litpic pubdate senddate arcatt adminID memberID description keywords templet lastpost postnum redirecturl mtype userip ";
function GetFields()
{
	var issystem = document.form1.issystem.value;
	var fieldname = document.form1.fname.value;
	var itemname = document.form1.itemname.value;
	var dtype = document.form1.dtype.value;
	var isnull = document.form1.isnull.value;
	var vdefault = document.form1.vdefault.value;
	var maxlength = document.form1.maxlength.value;
	var vfunction = document.form1.vfunction.value;
	var vinnertext = document.form1.vinnertext.value;
	if(document.form1.spage[0].checked) var spage = document.form1.spage[0].value;
	else var spage = document.form1.spage[1].value;
	if(isnull==0) var sisnull="false";
	else var sisnull="true";
	if(issystem!=1)
	{
	  if(notAllow.indexOf(" "+fieldname+" ") >-1 ) {
		  alert("字段名称不合法，如下名称是不允许的：\n"+notAllow);
		  return false;
	  }
	  if((dtype=="text"||dtype=="radio"||dtype=="select") && maxlength==""){
		  alert("你选择的是文本、select或radio类型，必须设置最大字符长度！");
		  return false;
	  }
	  if((dtype=="radio"||dtype=="select") && vinnertext==""){
		  alert("你选择的select或radio类型，必须在表单HTML里设置选择的项目（用逗号[,]分开）！");
		  return false;
	  }
	  if(itemname==""){
		  alert("表单提示名称不能为空！");
		  return false;
	  }
	}
	if(spage=="no") spage = "";
	revalue =  "<field:"+fieldname+" itemname=\""+itemname+"\" type=\""+dtype+"\"";
	revalue += " isnull=\""+sisnull+"\" default=\""+vdefault+"\" function=\""+vfunction+"\"";
	revalue += " maxlength=\""+maxlength+"\" page=\""+spage+"\">"+vinnertext+"</field:"+fieldname+">\r\n";
	document.form1.editfield.value = revalue;
	document.form1.submit();
	return true;
}
</script>
<style type="text/css">
<!--
.STYLE1 {color: #FF0000}
-->
</style>
</head>
<body topmargin="8">
<table width="98%"  border="0" align="center" cellpadding="2" cellspacing="1" bgcolor="#98CAEF">
  <form name="form1" action="mychannel_field_edit.php" method="post">
  	<input type='hidden' name='action' value='save'>
  	<input type='hidden' name='ID' value='<?php echo $ID?>'>
  	<input type='hidden' name='fname' value='<?php echo $fname?>'>
	  <input type='hidden' name='issystem' value='<?php echo $issystem?>'>
	  <input type='hidden' name='editfield' value=''>
    <tr> 
      <td height="20" colspan="2" background="img/tbg.gif"> 
        <table width="98%" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td width="30%"><b>&nbsp;<a href="mychannel_main.php"></a>查看/修改字段：</b>            </td>
            <td align="right">
			<input type="button" name="ss1" value="当前模型信息" style="width:90px;margin-right:6px" onClick="location='mychannel_edit.php?ID=<?php echo $ID?>&dopost=edit';" class='nbt'>
              <input type="button" name="ss12" value="频道模型管理" style="width:90px;margin-right:6px" onClick="location='mychannel_main.php';" class='nbt'>            </td>
          </tr>
        </table> </td>
    </tr>
    <?php
	if($issystem==1){
	?>
	<tr>
      <td colspan="2" align="center" bgcolor="#FFFFFF"><span class="STYLE1">你当前在查看的是系统模型的字段，这些内容是不能更改的！</span></td>
    </tr>
	<?php } ?>
    <tr> 
      <td align="center" bgcolor="#FFFFFF">表单提示文字：</td>
      <td bgcolor="#FFFFFF">
      	<input name="itemname" type="text" id="itemname" value="<?php echo $ctag->GetAtt('itemname')?>" style="width:35%">
        *（发布内容时显示的项名字）        </td>
    </tr>
    <tr> 
      <td width="28%" align="center" bgcolor="#FFFFFF">字段名称：</td>
      <td width="72%" bgcolor="#FFFFFF" style="table-layout:fixed;word-break:break-all"> 
        <?php echo $fname?>      </td>
    </tr>
    <tr> 
      <td align="center" bgcolor="#FFFFFF">数据类型：</td>
      <td bgcolor="#FFFFFF">
      	<select name="dtype" id="type" style="width:250">
          <?php 
          $dtype = $ctag->GetAtt('type');
          if($dtype!='' && isset($fieldtypes[$dtype])) echo "          <option value='{$dtype}'>{$fieldtypes[$dtype]}</option>\r\n";
          else echo "          <option value='{$dtype}'>{$dtype}</option>\r\n";
          ?>
          <option value="text">单行文本</option>
          <option value="multitext">多行文本</option>
          <option value="htmltext">HTML文本</option>
          <option value="int">整数类型</option>
          <option value="float">小数类型</option>
          <option value="datetime">时间类型</option>
          <option value="img">图片</option>
          <option value="media">多媒体文件</option>
          <option value="addon">附件类型</option>
          <option value="textdata">文本存放数据</option>
          <option value="select">使用option下拉框</option>
          <option value="radio">使用radio选项卡</option>
        </select> </td>
    </tr>
    <tr> 
      <td align="center" bgcolor="#FFFFFF">是否分页：</td>
      <td bgcolor="#FFFFFF">
      	<input name="isnull" type="hidden" value="1">
        <input name="spage" type="radio" class="np" value="split"<?php  if($ctag->GetAtt('page')=='split') echo " checked";?>>
        是 &nbsp; 
        <input name="spage" type="radio" class="np" value="no"<?php  if($ctag->GetAtt('page')=='no'||$ctag->GetAtt('page')=='') echo " checked";?>>
        否 
        </td>
      </tr>
    <tr> 
      <td align="center" bgcolor="#FFFFFF">默认值：</td>
      <td bgcolor="#FFFFFF"> 
      	<input name="vdefault" type="text" id="vdefault" value="<?php echo $ctag->GetAtt('default')?>">      </td>
    </tr>
    <tr> 
      <td align="center" bgcolor="#FFFFFF">最大长度：</td>
      <td bgcolor="#FFFFFF">
      	<input name="maxlength" type="text" id="maxlength" value="<?php echo $ctag->GetAtt('maxlength')?>">
        (文本数据必须填写，大于255为text类型)        </td>
    </tr>
    <tr> 
      <td align="center" bgcolor="#FFFFFF">处理函数：</td>
      <td bgcolor="#FFFFFF">
      	<input name="vfunction" type="text" id="vfunction" value="<?php echo $ctag->GetAtt('function')?>" style="width:35%">
        (可选，用'@me'表示当前项目值参数)       </td>
    </tr>
    <tr>
      <td align="center" bgcolor="#FFFFFF">自定义表单HTML：</td>
      <td bgcolor="#FFFFFF">
      	◆自定义表单HTML时，表单名必须为“字段名称”，value='@value'，<br>
      	◆如果定义数据类型为select或radio时，此处填写被选择的项目，用“,”分开，如“男,女,人妖”。      </td>
    </tr>
    <tr> 
      <td align="center" bgcolor="#FFFFFF"><br>      </td>
      <td bgcolor="#FFFFFF">
      	<textarea name="vinnertext" cols="45" rows="5" id="vinnertext"><?php echo $ctag->GetInnerText()?></textarea>      </td>
    </tr>
    <tr> 
      <td height="28" colspan="2" bgcolor="#E8F8FF">
	  <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td width="26%" height="45">&nbsp;</td>
            <td width="20%"><img src="img/button_ok.gif" width="60" height="22" border="0" style="cursor:hand" onClick="GetFields()"></td>
            <td width="54%"><img src="img/button_reset.gif" width="60" height="22" border="0" style="cursor:hand" onClick="form1.reset()"></td>
          </tr>
        </table></td>
    </tr>
  </form>
</table>
<?php  $dsql->Close(); ?>
</body>
</html>