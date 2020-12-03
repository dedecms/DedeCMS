<?
require_once(dirname(__FILE__)."/config.php");
require_once(dirname(__FILE__)."/../include/pub_dedetag.php");
require_once(dirname(__FILE__)."/../include/inc_typelink.php");
if($nid=="") 
{
	ShowMsg("参数无效!","-1");	
	exit();
}
$dsql = new DedeSql(false);
$dsql->SetSql("Select * from #@__conote where nid='$nid'");
$dsql->Execute();
$rowFirst = $dsql->GetObject();
$gathername = $rowFirst->gathername;
$typeid = $rowFirst->typeid;
$noteinfo = $rowFirst->noteinfo;
$language = $rowFirst->language;
$pos = strpos($noteinfo,"{dede:comments}",strlen("{dede:comments}"));
$headinfo = substr($noteinfo,0,$pos);
$otherinfo = substr($noteinfo,$pos,strlen($noteinfo)-$pos);
$otherinfo = eregi_replace("<textarea","< textarea",$otherinfo);
$otherinfo = eregi_replace("</textarea","< /textarea",$otherinfo);
$otherinfo = eregi_replace("<form","< form",$otherinfo);
$otherinfo = eregi_replace("</form","< /form",$otherinfo);
$dsql->FreeResult();

$dtp = new DedeTagParse();
$dtp->SetNameSpace("dede","{","}");
$dtp->LoadString($headinfo);
$ctag = $dtp->GetTag("item");
$dtp->Clear();

$imgurl = $ctag->GetAtt("imgurl");
$imgdir = $ctag->GetAtt("imgdir");


?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>采集节点分类</title>
<link href="base.css" rel="stylesheet" type="text/css">
</head>
<body background='img/allbg.gif' leftmargin='8' topmargin='8'>
<table width="98%" border="0" cellpadding="3" cellspacing="1" bgcolor="#666666" align="center">
  <form name="form1" action="action_co_edit.php" method="post">
  	<input type="hidden" name="nid" value="<?=$nid?>">
  <tr> 
    <td height="20" background='img/tbg.gif'> <table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr> 
          <td width="37%" height="18"><strong>更改采集节点：</strong></td>
          <td width="63%" align="right">&nbsp;<input type="button" name="b11" value="返回采集节点管理页" class="np2" style="width:160" onClick="location.href='co_main.php';"></td>
        </tr>
      </table></td>
  </tr>
  <tr> 
    <td height="24" bgcolor="#F2F6E5"><table width="400" border="0" cellspacing="0" cellpadding="0">
        <tr> 
          <td width="26" align="center"><img src="img/file_tt.gif" width="7" height="8"></td>
          <td width="374">节点基本信息</td>
        </tr>
      </table></td>
  </tr>
  <tr> 
    <td height="94" bgcolor="#FFFFFF">
	<table width="98%" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td width="18%" height="24">节点名称：</td>
            <td width="32%"><input name="notename" value="<?=$gathername?>" type="text" id="notename" style="width:150"></td>
            <td width="18%">页面编码：</td>
            <td width="32%">
            	<input type="radio" name="language" class="np" value="gb2312"<? if($language=="gb2312") echo " checked"; ?>>
              GB2312 
              <input type="radio" name="language" class="np" value="utf-8"<? if($language=="utf-8") echo " checked"; ?>>
              UTF8 
              <input type="radio" name="language" class="np" value="big5"<? if($language=="big5") echo " checked"; ?>>
              BIG5 </td>
          </tr>
          <tr> 
            <td height="24">图片相对网址：</td>
            <td><input name="imgurl" value="<?=$imgurl?>" type="text" id="imgurl" style="width:150"></td>
            <td>物理路径：</td>
            <td><input name="imgdir"  value="<?=$imgdir?>" type="text" id="imgdir" style="width:150"></td>
          </tr>
          <tr> 
            <td height="24">导出分类ID：</td>
            <td colspan="3"> 
              <?
       if(empty($typeid)) $typeid="0";
       $tl = new TypeLink($typeid);
       $typeOptions = $tl->GetOptionArray($typeid,$cuserLogin->getUserChannel(),1);
       echo "<select name='typeid' style='width:200'>\r\n";
       if($typeid=="0") echo "<option value='0' selected>请选择分类...</option>\r\n";
       echo $typeOptions;
       echo "</select>";
	   $tl->Close();
		?>
            </td>
          </tr>
        </table></td>
  </tr>
  <tr>
    <td height="24" bgcolor="#F2F6E5">
    	<table width="400" border="0" cellspacing="0" cellpadding="0">
        <tr> 
          <td width="26" align="center"><img src="img/file_tt.gif" width="7" height="8"></td>
          <td width="374">其它规则</td>
        </tr>
      </table></td>
  </tr>
  <tr> 
    <td height="360" bgcolor="#FFFFFF" align="center">
	<textarea name="otherconfig" id="otherconfig" style="width:96%;height:350"><?=$otherinfo?></textarea>
	</td>
  </tr>
  <tr> 
    <td height="36" bgcolor="#FAFAF1">
    	<table width="400" border="0" cellspacing="0" cellpadding="0">
        <tr> 
          <td width="26" align="center">&nbsp;</td>
          <td width="374"><input type="submit" name="b12" value="保存更改" class="coolbg" style="width:80"></td>
        </tr>
      </table></td>
  </tr>
</form>
</table>
</body>
</html>
<?
$dsql->Close();
?>
