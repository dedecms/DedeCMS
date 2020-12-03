<?php
require_once(dirname(__FILE__)."/../../../dialoguser/config.php");
require_once(dirname(__FILE__)."/../../../inc_photograph.php");
if(empty($dopost)) $dopost="";
if(empty($imgwidthValue)) $imgwidthValue=400;
if(empty($imgheightValue)) $imgheightValue=300;
if(empty($urlValue)) $urlValue="";
if(empty($imgsrcValue)) $imgsrcValue="";
if(empty($imgurl)) $imgurl="";
if(empty($dd)) $dd="";
if($dopost=="upload")
{
	if(empty($imgfile)) $imgfile="";
	if(!is_uploaded_file($imgfile)){
		 ShowMsg("你没有选择上传的文件!","-1");
	   exit();
	}
	if($imgfile_size > $cfg_mb_upload_size*1024){
	   @unlink(is_uploaded_file($imgfile));
		 ShowMsg("你上传的文件超过了{$cfg_mb_upload_size}K，不允许上传！","-1");
		 exit();
	}
	if(!eregi("\.(jpg|gif|png|bmp)$",$imgfile_name)){
		ShowMsg("你所上传的文件类型被禁止！","-1");
		exit();
	}
	CheckUserSpace($cfg_ml->M_ID);
	$sparr = Array("image/pjpeg","image/jpeg","image/gif","image/png","image/x-png","image/wbmp");
  $imgfile_type = strtolower(trim($imgfile_type));
  if(!in_array($imgfile_type,$sparr)){
		ShowMsg("上传的图片格式错误，请使用JPEG、GIF、PNG、WBMP格式的其中一种！","-1");
		exit();
	}
	if($imgfile_type=='image/pjpeg'||$imgfile_type=='image/jpeg'){
		$sname = '.jpg';
	}else if($imgfile_type=='image/gif'){
		$sname = '.gif';
	}else if($imgfile_type=='image/png'){
		$sname = '.png';
	}else if($imgfile_type=='image/wbmp'){
		$sname = '.bmp';
	}else{
		$sname = '.jpg';
	}
	$nowtime = time();
	$savepath = $cfg_user_dir."/".$cfg_ml->M_ID;
  CreateDir($savepath);
  CloseFtp();
  $rndname = dd2char($cfg_ml->M_ID."0".strftime("%y%m%d%H%M%S",$nowtme)."0".mt_rand(1000,9999));
	$filename = $savepath."/".$rndname;
	$rndname  = $rndname.$sname; //仅作注解用

  //大小图URL
  $bfilename = $filename.$sname;
	$litfilename = $filename."_lit".$sname;

  //大小图真实地址
  $fullfilename = $cfg_basedir.$bfilename;
  $full_litfilename = $cfg_basedir.$litfilename;

  if(file_exists($fullfilename)){
  	ShowMsg("本目录已经存在同名的文件，请重试！","-1");
		exit();
  }

  //严格检查最终的文件名
  if(eregi("\.(php|asp|pl|shtml|jsp|cgi|aspx)",$fullfilename)){
		ShowMsg("你所上传的文件类型被禁止，系统只允许上传<br>".$cfg_mb_mediatype." 类型附件！","-1");
		exit();
	}

  @move_uploaded_file($imgfile,$fullfilename);

	$dsql = new DedeSql(false);
	if($dd=="yes")
	{
			copy($fullfilename,$full_litfilename);
			if(in_array($imgfile_type,$cfg_photo_typenames)) ImageResize($full_litfilename,$w,$h);
			$urlValue = $bfilename;
			$imgsrcValue = $litfilename;
			$info = "";
			$sizes = getimagesize($full_litfilename,$info);
			$imgwidthValue = $sizes[0];
	    $imgheightValue = $sizes[1];
	    $imgsize = filesize($full_litfilename);
	    $inquery = "
       INSERT INTO #@__uploads(title,url,mediatype,width,height,playtime,filesize,uptime,adminid,memberid)
       VALUES ('对话框上传 {$rndname} 的小图','$imgsrcValue','1','$imgwidthValue','$imgheightValue','0','{$imgsize}','{$nowtime}','0','".$cfg_ml->M_ID."');
     ";
     $dsql->ExecuteNoneQuery($inquery);
	}else{
		$imgsrcValue = $bfilename;
		$urlValue = $bfilename;
		$info = "";
		$sizes = getimagesize($fullfilename,$info);
		$imgwidthValue = $sizes[0];
	  $imgheightValue = $sizes[1];
	  $imgsize = filesize($fullfilename);
	}
	$info = '';
	$bsizes = getimagesize($fullfilename,$info);
  $bimgwidthValue = $bsizes[0];
	$bimgheightValue = $bsizes[1];
	$bimgsize = filesize($fullfilename);
	$inquery = "
    INSERT INTO #@__uploads(title,url,mediatype,width,height,playtime,filesize,uptime,adminid,memberid)
    VALUES ('对话框上传的图片','$bfilename','1','$bimgwidthValue','$bimgheightValue','0','{$bimgsize}','{$nowtime}','0','".$cfg_ml->M_ID."');
   ";
  $dsql->ExecuteNoneQuery($inquery);
  $dsql->Close();
	if(in_array($imgfile_type,$cfg_photo_typenames) &&( $imgfile_type!='image/gif' || $cfg_gif_wartermark=='Y')) WaterImg($fullfilename,'up');
	$kkkimg = $urlValue;
}
if(empty($kkkimg)) $kkkimg="picview.gif";
if(!eregi("^http:",$imgsrcValue)){
  $imgsrcValue = ereg_replace("/{1,}",'/',$imgsrcValue);
  $urlValue = ereg_replace("/{1,}",'/',$urlValue);
}
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
	isrc = document.form1.imgsrc.value;
	iurl = document.form1.url.value;
	if(ialign!=0) ialign = " align='"+ialign+"'";
	inImg  = "<img src='"+ isrc +"' width='"+ imgwidth;
	inImg += "' height='"+ imgheight +"' border='"+ iborder +"' alt='"+ ialt +"'"+ialign+"/>";
	if(iurl!="") inImg = "<a href='"+ iurl +"' target='_blank'>"+ inImg +"</a>\r\n";
	if(document.all) oDOM.selection.createRange().pasteHTML(inImg);
	else FCK.InsertHtml(inImg);
  window.close();
}
function SelectMedia(fname)
{
   var posLeft = 150;
   var posTop = 100;
   window.open("../../../dialoguser/select_images.php?f="+fname+"&imgstick=big", "popUpImgWin", "scrollbars=yes,resizable=yes,statebar=no,width=600,height=450,left="+posLeft+", top="+posTop);
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
<form enctype="multipart/form-data" name="form1" id="form1" method="post">
<input type="hidden" name="dopost" value="upload">
<input type="hidden" name="himgheight" value="<?php echo $imgheightValue?>">
<input type="hidden" name="himgwidth" value="<?php echo $imgwidthValue?>">
  <table width="100%" border="0">
    <tr height="20">
      <td colspan="3">
      <fieldset>
        <legend>图片属性</legend>
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="65" height="25" align="right">网址：</td>
            <td colspan="2">
            	<input name="imgsrc" type="text" id="imgsrc" size="30" onChange="SeePic('picview',this);" value="<?php echo $imgsrcValue?>">
              <input onClick="SelectMedia('form1.imgsrc');" type="button" name="selimg" value=" 浏览... " class="binput" style="width:80">
            </td>
          </tr>
          <tr>
            <td height="25" align="right">宽度：</td>
            <td colspan="2" nowrap>
			 <input type="text"  id="imgwidth" name="imgwidth" size="8" value="<?php echo $imgwidthValue?>" onChange="UpImgSizeW()">
              &nbsp;&nbsp; 高度:
              <input name="imgheight" type="text" id="imgheight" size="8" value="<?php echo $imgheightValue?>" onChange="UpImgSizeH()">
              <input type="button" name="Submit" value="原始" class="binput" style="width:40" onclick="UpdateImageInfo()">
              <input name="autoresize" type="checkbox" id="autoresize" value="1" checked>
              自适应</td>
          </tr>
          <tr>
            <td height="25" align="right">边框：</td>
            <td colspan="2" nowrap><input name="border" type="text" id="border" size="4" value="0">
              &nbsp;替代文字:
              <input name="alt" type="text" id="alt" size="10"></td>
          </tr>
          <tr>
            <td height="25" align="right">链接：</td>
            <td width="166" nowrap><input name="url" type="text" id="url" size="30"   value="<?php echo $urlValue?>"></td>
            <td width="155" align="center" nowrap>&nbsp;</td>
          </tr>
		  <tr>
            <td height="25" align="right">对齐：</td>
            <td nowrap><select name="ialign" id="ialign">
                <option value="0" selected>默认</option>
                <option value="right">右对齐</option>
                <option value="center">中间</option>
                <option value="left">左对齐</option>
                <option value="top">顶端</option>
                <option value="bottom">底部</option>
              </select></td>
            <td align="right" nowrap>
            	<input onClick="ImageOK();" type="button" name="Submit2" value=" 确定 " class="binput">&nbsp;
            </td>
          </tr>
        </table>
        </fieldset>
        </td>
    </tr>
    <tr height="25">
      <td colspan="3" nowrap> <fieldset>
        <legend>上传新图片</legend>
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr height="30">
            <td align="right" nowrap>　新图片：</td>
            <td colspan="2" nowrap><input name="imgfile" type="file" id="imgfile" onChange="SeePic('picview',this);" style="height:22" class="binput">
              &nbsp; <input type="submit" name="picSubmit" id="picSubmit" value=" 上 传  " style="height:22" class="binput"></td>
          </tr>
          <tr height="30">
            <td align="right" nowrap>　选　项：</td>
            <td colspan="2" nowrap>
			<input type="checkbox" name="dd" value="yes">生成缩略图
				&nbsp;
			缩略图宽度
              <input name="w" type="text" value="<?php echo $cfg_ddimg_width?>" size="3">
		   缩略图高度
              <input name="h" type="text" value="<?php echo $cfg_ddimg_height?>" size="3">
			</td>
          </tr>
        </table>
        </fieldset></td>
    </tr>
    <tr height="50">
      <td height="140" align="right" nowrap>预览区:</td>
      <td height="140" colspan="2" nowrap>
	  <table width="150" height="120" border="0" cellpadding="1" cellspacing="1">
          <tr>
            <td align="center"><img name="picview" id="picview" src="<?php echo $kkkimg?>" width="160" height="120" alt="预览图片"></td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</form>
</body>
</HTML>
