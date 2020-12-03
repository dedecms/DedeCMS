<?
require_once(dirname(__FILE__)."/config.php");
if(empty($dopost)) $dopost = "";

if($dopost=="upload") //上传
{
require_once(dirname(__FILE__)."/../include/inc_photograph.php");
$sparr_image = Array("image/pjpeg","image/jpeg","image/gif","image/png","image/x-png","image/wbmp");
$sparr_flash = Array("application/x-shockwave-flash");
$okdd = 0;
$uptime = mytime();
$adminid = $cuserLogin->getUserID();
$dsql = new DedeSql(false);
for($i=0;$i<=40;$i++)
{
  if(isset(${"upfile".$i}) && is_uploaded_file(${"upfile".$i}))
  {
		$filesize = ${"upfile".$i."_size"};
		$upfile_type = ${"upfile".$i."_type"};
		$upfile_name = ${"upfile".$i."_name"};
		$dpath = strftime("%y%m%d",$uptime);
		if(in_array($upfile_type,$sparr_image)){
			$mediatype=1;
			$savePath = $cfg_image_dir."/".$dpath;
		}
		else if(in_array($upfile_type,$sparr_false)){
			$mediatype=2;
			$savePath = $cfg_other_medias."/".$dpath;
		}
		else if(eregi('audio|media|video',$upfile_type)
	  && eregi("\.".$cfg_softtype."$",$upfile_name)){
		  $mediatype=3;
		  $savePath = $cfg_other_medias."/".$dpath;
		}
		else if(eregi("\.".$cfg_softtype."$",$upfile_name)){
			$mediatype=4;
			$savePath = $cfg_soft_dir."/".$dpath;
		}
		else continue;
		$filename = "{$adminid}_".strftime("%H%M%S",$uptime).mt_rand(100,999).$i;
		$fs = explode(".",${"upfile".$i."_name"});
		$filename = $filename.".".$fs[count($fs)-1];
		$filename = $savePath."/".$filename;
		if(!is_dir($cfg_basedir.$savePath)){
			MkdirAll($cfg_basedir.$savePath,777);
			CloseFtp();
		}
		$fullfilename = $cfg_basedir.$filename;
    if($mediatype==1){
       @move_uploaded_file(${"upfile".$i},$fullfilename);
       $info = "";
    	 $data = getImagesize($fullfilename,$info);
    	 $width = $data[0];
    	 $height = $data[1];
		 	if(in_array($upfile_type,$cfg_photo_typenames)) WaterImg($fullfilename,'up');
    }else{
       @move_uploaded_file(${"upfile".$i},$fullfilename);
    }
    if($i>1){ $ntitle = $title."_".$i; }
    else $ntitle = $title;
    $inquery = "
       INSERT INTO #@__uploads(title,url,mediatype,width,height,playtime,filesize,uptime,adminid,memberid) 
       VALUES ('$ntitle','$filename','$mediatype','$width','$height','$playtime','$filesize','$uptime','$adminid','0');
    ";
    $okdd++;
    $dsql->SetQuery($inquery);
    $dsql->ExecuteNoneQuery();
  }
}
$dsql->Close();
ShowMsg("成功上传 {$okdd} 个文件！","media_main.php");
exit();
}

?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=gb2312'>
<title>上传新文件</title>
<link href='base.css' rel='stylesheet' type='text/css'>
<style>
.coolbg2 {
border: 1px solid #000000;
background-color: #F2F5E9;
height:18px
}
</style>
<script language='javascript'>
function CheckSubmit()
{
	if(document.form1.title.value==""){
		alert("请设定媒体标题！");
		document.form1.title.focus();
		return false;
	}
}
var startNum = 6;
function MakeUpload()
{
   var upfield = document.getElementById("uploadfield");
   var endNum =  Number(document.form1.picnum.value)+startNum;
   if(endNum>40) endNum = 40;
   for(startNum;startNum<=endNum;startNum++){
	   upfield.innerHTML += "<input type='file' name='upfile"+startNum+"' style='width:300'/><br/>";
   }
}
function ResetUpload()
{
   var upfield = document.getElementById("uploadfield");
   upfield.innerHTML = "";
   startNum = 2;
}
</script>
</head>
<body background='img/allbg.gif' leftmargin='8' topmargin='8'>
<table width="99%" border="0" cellpadding="3" cellspacing="1" bgcolor="#666666">
  <tr> 
    <td height="19" background='img/tbg.gif'>
    	<table width="98%" border="0" cellpadding="0" cellspacing="0">
        <tr> 
          <td width="2%" align="center" valign="middle"><img src="img/item_tt2.gif" width="7" height="15"></td>
          <td width="44%"><strong>上传新文件：</strong></td>
          <td width="54%" align="right">[<a href='media_main.php'><u>附件/媒体数据管理</u></a>]</td>
        </tr>
      </table></td>
  </tr>
  <tr> 
    <td height="19" bgcolor="#ffffff">
    	 <img src="img/help.gif" border="0">
    	提示：图片类型仅支持jpg、png、gif、wbmp格式，flash为.swf格式，视音频和附件为限定扩展名的类型(可在系统参数中修改)。
    </td>
  </tr>
  <tr> 
    <td height="69" align="center" valign="top" bgcolor="#FFFFFF">
    	<table width="100%" border="0" cellpadding="0" cellspacing="0">
        <form enctype="multipart/form-data" name='form1' action="media_add.php" method="POST" onSubmit="return CheckSubmit();">
          <input type="hidden" name="dopost" value="upload">
          <tr> 
            <td width="15%" height="30" align="center" bgcolor="#FFFFFF"  class='bline'>媒体类型：</td>
            <td width="85%" height="25" bgcolor="#FFFFFF"  class='bline'>
            	<input name="mediatype" type="radio" class="np" value="1" checked>
              图片 
              <input type="radio" name="mediatype" class="np" value="2">
              FLASH 
              <input type="radio" name="mediatype" class="np" value="3">
              视频/音频 
              <input type="radio" name="mediatype" class="np" value="4">
              附件/其它
            </td>
          </tr>
          <tr> 
            <td height="30" align="center" bgcolor="#FFFFFF"  class='bline'>说明标题：</td>
            <td height="25" bgcolor="#FFFFFF"  class='bline'><input name="title" type="text" id="title" size="30"></td>
          </tr>
          <tr> 
            <td height="30" align="center" bgcolor="#FFFFFF"  class='bline'>参数说明：</td>
            <td height="30" bgcolor="#FFFFFF"  class='bline' colspan='2'>图片不需要指定“宽”、“高”，其它附加参数仅方便多媒体文件管理，没其它含义</td>
          </tr>
          <tr> 
            <td height="30" align="center" bgcolor="#FFFFFF" class='bline'>附加参数：</td>
            <td height="25" bgcolor="#FFFFFF" class='bline'>宽： 
              <input name="mediawidth" type="text" id="mediawidth" size="5">
              (像素)　高： 
              <input name="mediaheight" type="text" id="mediaheight" size="5">
              (像素)　播放时间： 
              <input name="playtime" type="text" id="mediawidth3" size="5">
              (分钟)</td>
          </tr>
          <tr>
            <td height="30" align="center" bgcolor="#FFFFFF"  class='bline'>上传文件：</td>
            <td bgcolor="#FFFFFF"  class='bline'>数量：
              <input name="picnum" type="text" id="picnum" value="5" size="6">
              <input type="button" name="Submit" value="增加" onClick="MakeUpload()">
			  &nbsp;
              <input type="button" name="Submit" value="恢复" onClick="ResetUpload()">
			 </td>
          </tr>
          <tr> 
            <td height="25" align="center" bgcolor="#FFFFFF"  class='bline'>&nbsp;</td>
            <td bgcolor="#FFFFFF"  class='bline'>
			<input name="upfile1" type="file" id="upfile1" style='width:300'/><br/>
			<input name="upfile2" type="file" id="upfile2" style='width:300'/><br/>
			<input name="upfile3" type="file" id="upfile3" style='width:300'/><br/>
			<input name="upfile4" type="file" id="upfile4" style='width:300'/><br/>
			<input name="upfile5" type="file" id="upfile5" style='width:300'/><br/>
            <span id='uploadfield'></span>
			 </td>
          </tr>
          <tr> 
            <td height="62" colspan="2" bgcolor="#FFFFFF"  class='bline'> <table width="60%" border="0" cellspacing="0" cellpadding="0">
                <tr> 
                  <td align="center">
				  <input class="np" name="imageField" type="image" src="img/button_save.gif" width="60" height="22" border="0"> 
                  </td>
                </tr>
              </table></td>
          </tr>
        </form>
      </table> </td>
  </tr>
</table>
	</body>
</html>