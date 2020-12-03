<?
require_once(dirname(__FILE__)."/config.php");
CheckPurview('sys_Edit');
require_once(dirname(__FILE__)."/../include/inc_photograph.php");
if($cfg_photo_support==""){ echo "你的系统没安装GD库，不允许使用本功能！"; }
$ImageWaterConfigFile = dirname(__FILE__)."/../include/inc_photowatermark_config.php";
if(empty($action)) $action = "";
if($action=="save")
{
   $vars = array('photo_markup','photo_markdown','photo_wwidth','photo_wheight','photo_waterpos','photo_watertext','photo_fontsize','photo_fontcolor','photo_diaphaneity');
   $configstr = "";
   foreach($vars as $v){
   	 ${$v} = str_replace("'","",${$v});
   	 $configstr .= "\${$v} = '".${$v}."';\r\n";
   }
   $shortname = "";
   if(is_uploaded_file($newimg)){
   	  $imgfile_type = strtolower(trim($newimg_type));
      if(!in_array($imgfile_type,$cfg_photo_typenames)){
		  ShowMsg("上传的图片格式错误，请使用 {$cfg_photo_support}格式的其中一种！","-1");
		  exit();
	   }
	   if($imgfile_type=='image/bmp') $shortname = ".bmp";
	   else if($imgfile_type=='image/png') $shortname = ".png";
	   else if($imgfile_type=='image/gif') $shortname = ".gif";
	   else $shortname = ".jpg";
	   $photo_markimg = 'mark'.$shortname;
	   @move_uploaded_file($newimg,dirname(__FILE__)."/../include/data/".$photo_markimg);
   }
   $configstr .= "\$photo_markimg = '{$photo_markimg}';\r\n";
   $configstr = "<"."?\r\n".$configstr."?".">\r\n";
   $fp = fopen($ImageWaterConfigFile,"w") or die("写入文件 $ImageWaterConfigFile 失败，请检查权限！");
   fwrite($fp,$configstr);
   fclose($fp);
   echo "<script>alert('修改配置成功！');</script>\r\n";
}
require_once($ImageWaterConfigFile);
?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=gb2312'>
<title>系统配置参数 - 图片水印设置</title>
<link href='base.css' rel='stylesheet' type='text/css'>
</head>
<body background='img/allbg.gif' leftmargin='8' topmargin='8'>
<table width="98%" border="0" align="center" cellpadding="3" cellspacing="1" bgcolor="#666666">
  <form action="sys_info_mark.php" method="post" enctype="multipart/form-data">
  <input type="hidden" name="action" value="save">
  <input type="hidden" name="photo_markimg" value="<?=$photo_markimg?>">
  <tr> 
    <td height="26" colspan="2" bgcolor="#FFFFFF" background="img/tbg.gif">
	<b>DedeCms系统配置参数</b> - <strong>图片水印设置</strong>    </td>
  </tr>
  <tr bgcolor="#FFFFFF"> 
    <td width="41%" height="24">上传的图片是否使用图片水印功能：<br> </td>
    <td width="59%"> <input class="np" type="radio" value="1" name="photo_markup"<?if($photo_markup==1) echo ' checked';?>>
      开启 
      <input class="np" type="radio" value="0" name="photo_markup"<?if($photo_markup==0) echo ' checked';?>>
      关闭 </td>
  </tr>
  <tr bgcolor="#FFFFFF"> 
    <td height="24">采集的图片是否使用图片水印功能：</td>
    <td> <input class="np" type="radio" value="1" name="photo_markdown"<?if($photo_markdown==1) echo ' checked';?>>
      开启 
        <input class="np" type="radio" value="0" name="photo_markdown"<?if($photo_markdown==0) echo ' checked';?>>
      关闭 </td>
  </tr>
  <tr bgcolor="#FFFFFF"> 
    <td height="24">添加水印的图片大小控制（设置为0为不限）：</td>
    <td> 宽： 
      <input name="photo_wwidth" type=text id="photo_wwidth"   value="<?=$photo_wwidth?>" size="5">
      高： 
      <input name="photo_wheight" type=text id="photo_wheight"  value="<?=$photo_wheight?>" size="5"> </td>
  </tr>
  
  <tr bgcolor="#FFFFFF"> 
    <td height="24">水印图片文件名（如果不存在，则使用文字水印）：</td>
    <td><img src="../include/data/<?=$photo_markimg?>" alt="dede"></td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td height="24">上传新图片：</td>
    <td>
	<input name="newimg" type="file" id="newimg" style="width:300">
    <br>
	<? echo "你的系统支持的图片格式：".$cfg_photo_support; ?>
	</td>
  </tr>
  <tr bgcolor="#FFFFFF"> 
    <td height="24">水印图片文字（不支持中文）：</td>
    <td> <input type="text" name="photo_watertext"  value="<?=$photo_watertext?>"></td>
  </tr>
  <tr bgcolor="#FFFFFF"> 
    <td height="24">水印图片文字字体大小：</td>
    <td> <input name="photo_fontsize" type=text id="photo_fontsize"  value="<?=$photo_fontsize?>"></td>
  </tr>
  <tr bgcolor="#FFFFFF"> 
    <td height="24">水印图片文字颜色（默认#FF0000为红色）：</td>
    <td> <input name="photo_fontcolor" type=text id="photo_fontcolor"  value="<?=$photo_fontcolor?>"></td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td height="24">水印透明度（0—100，值越小越透明）：</td>
    <td><input name="photo_diaphaneity" type=text id="photo_diaphaneity"  value="<?=$photo_diaphaneity?>"></td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td height="24">水印位置：</td>
    <td>
	<input class="np" type="radio" name="photo_waterpos"  value="0"<?if($photo_waterpos==0) echo ' checked';?>>
          随机位置
	<table width="300" border="1" cellspacing="0" cellpadding="0">
      <tr>
        <td width="33%"><input class="np" type="radio" name="photo_waterpos"  value="1"<?if($photo_waterpos==1) echo ' checked';?>>
          顶部居左</td>
        <td width="33%"><input class="np" type="radio" name="photo_waterpos"  value="4"<?if($photo_waterpos==4) echo ' checked';?>>
          顶部居中</td>
        <td><input class="np" type="radio" name="photo_waterpos"  value="7"<?if($photo_waterpos==7) echo ' checked';?>>
          顶部居右</td>
      </tr>
      <tr>
        <td><input class="np" type="radio" name="photo_waterpos"  value="2"<?if($photo_waterpos==2) echo ' checked';?>>
          左边居中</td>
        <td><input class="np" type="radio" name="photo_waterpos"  value="5"<?if($photo_waterpos==5) echo ' checked';?>>
          图片中心</td>
        <td><input class="np" type="radio" name="photo_waterpos"  value="8"<?if($photo_waterpos==8) echo ' checked';?>>
          右边居中</td>
      </tr>
      <tr>
        <td><input class="np" type="radio" name="photo_waterpos"  value="3"<?if($photo_waterpos==3) echo ' checked';?>>
          底部居左</td>
        <td><input class="np" type="radio" name="photo_waterpos"  value="6"<?if($photo_waterpos==6) echo ' checked';?>>
          底部居中</td>
        <td><input name="photo_waterpos" type="radio" class="np"  value="9"<?if($photo_waterpos==9) echo ' checked';?>>
          底部居右</td>
      </tr>
    </table></td>
  </tr>
  
  <tr bgcolor="#F3FCE4"> 
    <td height="37" colspan="2">
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td width="7%">&nbsp;</td>
          <td width="93%">
		  <input name="imageField" class="np" type="image" src="img/button_ok.gif" width="60" height="22" border="0">
            　&nbsp;
		 <img src="img/button_reset.gif" width="60" height="22">		  </td>
        </tr>
      </table>	  </td>
  </tr>
  </form>
</table>
</body>
</html>
