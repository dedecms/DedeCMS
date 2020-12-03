<?php
require_once(dirname(__FILE__)."/../../../../member/config.php");
CheckRank(0,0);
require_once(dirname(__FILE__)."/../../../image.func.php");
require_once(dirname(__FILE__)."/../../../../member/inc/inc_archives_functions.php");
if(empty($dopost)) $dopost='';
if(empty($imgwidthValue)) $imgwidthValue=400;
if(empty($imgheightValue)) $imgheightValue=300;
if(empty($urlValue)) $urlValue='';
if(empty($imgsrcValue)) $imgsrcValue='';
if(empty($imgurl)) $imgurl='';
if(empty($dd)) $dd='';
if($dopost=='upload')
{
	$ntime = time();
	$cfg_ml->CheckUserSpace();
	$filename = MemberUploads('imgfile','',$cfg_ml->M_ID,'image','',-1,-1,true);
	$dfilename = ereg_replace("(.*)/","",$filename);
	SaveUploadInfo("对话框上传 {$dfilename} ",$filename,1);
	if($dd=="yes")
	{
		$litfilename = str_replace(".","-lit.",$filename);
		copy($cfg_basedir.'/'.$filename,$cfg_basedir.'/'.$litfilename);
		SaveUploadInfo("对话框上传 {$dfilename} 的小图",$litfilename,1);
		ImageResize($cfg_basedir.'/'.$litfilename,$w,$h);
		$urlValue = $filename;
		$imgsrcValue = $litfilename;
		$info = '';
		$sizes = getimagesize($cfg_basedir.'/'.$litfilename,$info);
		$imgwidthValue = $sizes[0];
		$imgheightValue = $sizes[1];
		$imgsize = filesize($cfg_basedir.'/'.$litfilename);
	}else{
		$imgsrcValue = $filename;
		$urlValue = $filename;
		$info = '';
		$sizes = getimagesize($cfg_basedir.'/'.$filename,$info);
		$imgwidthValue = $sizes[0];
		$imgheightValue = $sizes[1];
		$imgsize = filesize($cfg_basedir.'/'.$filename);
	}
	$kkkimg = $urlValue;
}
if(empty($kkkimg)) $kkkimg='picview.gif';
?>
<HTML>
<HEAD>
<title>插入图片</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<style>
td{font-size:10pt;}
</style>
<script language=javascript>
var oEditor	= window.parent.InnerDialogLoaded() ;
var oDOM		= oEditor.FCK.EditorDocument ;
var FCK = oEditor.FCK;
function ImageOK()
{
	var inImg,ialign,iurl,imgwidth,imgheight,ialt,isrc,iborder;
	ialign = document.form1.ialign.value;
	iborder = document.form1.border.value;
	imgwidth = document.form1.imgwidth.value;
	imgheight = document.form1.imgheight.value;
	ialt = document.form1.alt.value;
	<?php
	if($cfg_multi_site=='N')
	{
	?>
	isrc = document.form1.imgsrc.value;
	iurl = document.form1.url.value;
	<?php
  }
  else
  {
  echo "var basehost = '$cfg_basehost';\r\n";
	?>
	if(document.form1.imgsrc.value.indexOf('ttp:') <= 0)
	{
		isrc = basehost + document.form1.imgsrc.value;
	}
	else
	{
		isrc = document.form1.imgsrc.value;
	}
	if(document.form1.imgsrc.value.indexOf('ttp:') <= 0 && document.form1.imgsrc.value != '') {
		iurl = basehost + document.form1.url.value;
	}
	else
	{
		iurl = document.form1.url.value;
	}
	<?php
	}
	?>
	if(ialign!=0) ialign = " align='"+ialign+"'";
	inImg  = "<img src='"+ isrc +"' width='"+ imgwidth;
	inImg += "' height='"+ imgheight +"' border='"+ iborder +"' alt='"+ ialt +"'"+ialign+"/>";
	if(iurl!="") inImg = "<a href='"+ iurl +"' target='_blank'>"+ inImg +"</a>\r\n";
	//FCK.InsertHtml(inImg);
	var newCode = FCK.CreateElement('DIV');
  newCode.innerHTML = inImg;
  window.parent.Cancel();
}
function SelectMedia(fname)
{
   if(document.all){
     var posLeft = window.event.clientY-100;
     var posTop = window.event.clientX-400;
   }
   else{
     var posLeft = 100;
     var posTop = 100;
   }
   window.open("../../../../member/uploads_select.php?mediatype=1&f="+fname+"&imgstick=big", "popUpImgWin", "scrollbars=yes,resizable=yes,statebar=no,width=600,height=400,left="+posLeft+", top="+posTop);
}
function SeePic(imgid,fobj)
{
   if(!fobj) return;
   if(fobj.value != "" && fobj.value != null)
   {
     var cimg = document.getElementById(imgid);
     if(cimg) cimg.src = fobj.value;
   }
}
function UpdateImageInfo()
{
	var imgsrc = document.form1.imgsrc.value;
	if(imgsrc!="")
	{
	  var imgObj = new Image();
	  imgObj.src = imgsrc;
	  document.form1.himgheight.value = imgObj.height;
	  document.form1.himgwidth.value = imgObj.width;
	  document.form1.imgheight.value = imgObj.height;
	  document.form1.imgwidth.value = imgObj.width;
  }
}
function UpImgSizeH()
{
   var ih = document.form1.himgheight.value;
   var iw = document.form1.himgwidth.value;
   var iih = document.form1.imgheight.value;
   var iiw = document.form1.imgwidth.value;
   if(ih!=iih && iih>0 && ih>0 && document.form1.autoresize.checked)
   {
      document.form1.imgwidth.value = Math.ceil(iiw * (iih/ih));
   }
}
function UpImgSizeW()
{
   var ih = document.form1.himgheight.value;
   var iw = document.form1.himgwidth.value;
   var iih = document.form1.imgheight.value;
   var iiw = document.form1.imgwidth.value;
   if(iw!=iiw && iiw>0 && iw>0 && document.form1.autoresize.checked)
   {
      document.form1.imgheight.value = Math.ceil(iih * (iiw/iw));
   }
}
</script>
<link href="base.css" rel="stylesheet" type="text/css">
<base target="_self">
</HEAD>
<body bgcolor="#EBF6CD" leftmargin="4" topmargin="2">
<form enctype="multipart/form-data" name="form1" id="form1" method="post" />
<input type="hidden" name="dopost" value="upload" />
<input type="hidden" name="himgheight" value="<?php echo $imgheightValue?>" />
<input type="hidden" name="himgwidth" value="<?php echo $imgwidthValue?>" />
  <table width="100%" border="0">
    <tr height="20">
      <td colspan="3">
      <fieldset>
        <legend>图片属性</legend>
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="65" height="25" align="right">网址：</td>
            <td colspan="2">
            	<input name="imgsrc" type="text" id="imgsrc" size="30" onChange="SeePic('picview',this);" value="<?php echo $imgsrcValue?>" />
              <input onClick="SelectMedia('form1.imgsrc');" type="button" name="selimg" value=" 浏览... " class="binput" style="width:80px" />
            </td>
          </tr>
          <tr>
            <td height="25" align="right">宽度：</td>
            <td colspan="2" nowrap>
			        <input type="text"  id="imgwidth" name="imgwidth" size="8" value="<?php echo $imgwidthValue?>" onChange="UpImgSizeW()" />
              &nbsp;&nbsp; 高度:
              <input name="imgheight" type="text" id="imgheight" size="8" value="<?php echo $imgheightValue?>" onChange="UpImgSizeH()" />
              <input type="button" name="Submit" value="原始" class="binput" style="width:40px" onclick="UpdateImageInfo()" />
              <input name="autoresize" type="checkbox" id="autoresize" value="1" checked='1' />
              自适应
             </td>
          </tr>
          <tr>
            <td height="25" align="right">边框：</td>
            <td colspan="2" nowrap>
            	<input name="border" type="text" id="border" size="4" value="0" />
              &nbsp;替代文字:
              <input name="alt" type="text" id="alt" size="10" />
            </td>
          </tr>
          <tr>
            <td height="25" align="right">链接：</td>
            <td width="166" nowrap>
            	<input name="url" type="text" id="url" size="30"   value="<?php echo $urlValue?>" />
            </td>
            <td width="155" align="center" nowrap>&nbsp;</td>
          </tr>
		  <tr>
            <td height="25" align="right">对齐：</td>
            <td nowrap>
            	<select name="ialign" id="ialign">
                <option value="0" selected='1'>默认</option>
                <option value="right">右对齐</option>
                <option value="center">中间</option>
                <option value="left">左对齐</option>
                <option value="top">顶端</option>
                <option value="bottom">底部</option>
              </select>
              </td>
              <td align="right" nowrap>
            	  <input onClick="ImageOK();" type="button" name="Submit2" value=" 确定 " class="binput" />&nbsp;
              </td>
          </tr>
        </table>
        </fieldset>
        </td>
    </tr>
    <tr height="25">
      <td colspan="3" nowrap='1'>
      	<fieldset>
        <legend>上传新图片</legend>
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr height="30">
            <td align="right" nowrap>　新图片：</td>
            <td colspan="2" nowrap>
            	<input name="imgfile" type="file" id="imgfile" onChange="SeePic('picview',this);" style="height:22px" class="binput" />
              &nbsp; <input type="submit" name="picSubmit" id="picSubmit" value=" 上 传  " style="height:22px" class="binput" />
            </td>
          </tr>
          <tr height="30">
            <td align="right" nowrap>　选　项：</td>
            <td colspan="2" nowrap>
			       <input type="checkbox" name="dd" value="yes" />生成缩略图
				     &nbsp;
			      缩略图宽度
              <input name="w" type="text" value="<?php echo $cfg_ddimg_width?>" size="3" />
		         缩略图高度
           <input name="h" type="text" value="<?php echo $cfg_ddimg_height?>" size="3" />
			     </td>
          </tr>
        </table>
        </fieldset>
       </td>
    </tr>
    <tr height="50">
      <td height="140" align="right" nowrap>预览区:</td>
      <td height="140" colspan="2" nowrap>
	     <table width="150" height="120" border="0" cellpadding="1" cellspacing="1">
          <tr>
            <td align="center">
            	<img name="picview" id="picview" src="<?php echo $kkkimg?>" width="160" height="120" alt="预览图片" />
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</form>
</body>
</HTML>
