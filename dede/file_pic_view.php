<?
require_once(dirname(__FILE__)."/config.php");
CheckPurview('pic_view');
if(empty($activepath)) $activepath=$cfg_medias_dir;
$activepath = ereg_replace("/{1,}","/",$activepath);
$truePath = $cfg_basedir.$activepath;
$listSize=5;
function GetPrePath($nowPath)
{
	if($nowPath==""||$nowPath=="/")
		echo("当前为根目录\n");
	else
	{
		$dirs = split("/",$nowPath);
		$nowPath = "";
		for($i=1;$i<count($dirs)-1;$i++)
		{
			$nowPath .= "/".$dirs[$i];
		}
		echo("<a href=\"pic_view.php?activepath=".$nowPath."\">转到上级目录</a>\n");
	}
}
function ListPic($truePath,$nowPath)
{
    global $listSize;
    $col=0;
    $rowdd=0;
    $rowdd++;
    $imgfile="";
    $truePath = ereg_replace("/$","",ereg_replace("\\{1,}","/",trim($truePath)));
    $nowPath = ereg_replace("/$","",ereg_replace("/{1,}","/",trim($nowPath)));
    $dh = dir($truePath);
    echo("<tr align='center'>\n");
    while($filename=$dh->read())
    {
    	if(!ereg("\.$",$filename))
    	{

    		$fullName = $truePath."/".$filename;
    		$fileUrl =  $nowPath."/".$filename;
    		if(is_dir($fullName))
    		{
    		if($col%$listSize==0&&$col!=0)
			{
			echo("</tr>\n<tr align='center'>\n");
			for($i=$rowdd-$listSize;$i<$rowdd;$i++)
			{
				echo("<td>".$filelist[$i]."</td>\n");
			}
			echo("</tr>\n<tr align='center'>\n");
			}
    		$line = "
    		<td>
    		<table width='106' height='106' border='0' cellpadding='0' cellspacing='1' bgcolor='#CCCCCC'>
    		<tr><td align='center' bgcolor='#FFFFFF'>
    		<a href='pic_view.php?activepath=".$fileUrl."'>
    		<img src='img/pic_dir.gif' width='44' height='42' border='0'>
    		</a></td></tr></table></td>";
    		$filelist[$rowdd] = $filename;
			$col++;
			$rowdd++;
			echo $line;
    		}
    		else if(IsImg($filename))
    		{
    		if($col%$listSize==0&&$col!=0)
			{
			echo("</tr>\n<tr align='center'>\n");
			for($i=$rowdd-$listSize;$i<$rowdd;$i++)
			{
				echo("<td>".$filelist[$i]."</td>\n");
			}
			echo("</tr>\n<tr align='center'>\n");
			}
    		$line = "
    		<td>
    		<table width='106' height='106' border='0' cellpadding='0' cellspacing='1' bgcolor='#CCCCCC'>
    		<tr>
		    <td align='center' bgcolor='#FFFFFF'>
		    ".GetImgFile($truePath,$nowPath,$filename)."
		    </td>
			</tr></table></td>";
			$filelist[$rowdd] = $filename;
			$col++;
			$rowdd++;
			echo $line;
    	    }
    	}
    }
    echo("</tr>\n");
    if(!empty($filelist))
    {
    	echo("<tr align='center'>\n");
    	$t = ($rowdd-1)%$listSize;
    	if($t==0) $t=$listSize;
		for($i=$rowdd-$t;$i<$rowdd;$i++)
		{
			echo("<td>".$filelist[$i]."</td>\n");
		}
		echo("</tr>\n");
	}
        
}
function GetImgFile($truePath,$nowPath,$fileName)
{
	$toW=102;
	$toH=102;
	$srcFile = $truePath."/".$fileName;
	$info = "";
	$data = GetImageSize($srcFile,$info);
	$srcW=$data[0];
	$srcH=$data[1];
	if($toW>=$srcW&&$toH>=$srcH)
	{
		$ftoW=$srcW;
		$ftoH=$srcH;
	}
	else
	{
		$toWH=$toW/$toH;
        $srcWH=$srcW/$srcH;
        if($toWH<=$srcWH)
		{
           $ftoW=$toW;
           $ftoH=$ftoW*($srcH/$srcW);
		}
		else
		{
           $ftoH=$toH;
           $ftoW=$ftoH*($srcW/$srcH);
		}
	} 
	 return("<a href='".$nowPath."/".$fileName."' target='_blank'><img src='".$nowPath."/".$fileName."' width='".$ftoW."' height='".$ftoH."' border='0'></a>");
} 
function IsImg($fileName)
{
	if(ereg("\.(jpg|gif|png)$",$fileName)) return 1;
	else return 0;
}          
</script>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>图片浏览器</title>
<link href="base.css" rel="stylesheet" type="text/css">
<script language="javascript">
function getPic(str)
{
	
	var ss = new Array(3);
	ss = str.split("@");
	window.opener.document.form1.imgsrc.value=ss[0];
	window.opener.document.form1.imgwidth.value=ss[1];
	window.opener.document.form1.imgheight.value=ss[2];
	window.opener.document.form1.picview.src=ss[0];
	window.opener=true;
    window.close();
}
</script>
</head>
<body leftmargin="0" topmargin="0">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
<form name="form1">
  <tr> 
    <td height="10" colspan="<?=$listSize?>"></td>
  </tr>
  <tr> 
    <td colspan="<?=$listSize?>">
	<table width="98%" border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="#CCCCCC">
        <tr> 
          <td width="4%" align="center" bgcolor="#F9F9F7" height="24"><img src="img/file_dir.gif" width="16" height="16"></td>
          <td width="55%" align="center" bgcolor="#F9F9F7"><input name="activepath" type="text" id="path" size="30"  style="height:15pt" value="<?=$activepath?>"></td>
          <td width="20%" bgcolor="#F9F9F7">
          <table width="100%" border="0" cellspacing="1" cellpadding="0">
                <tr> 
                  <td width="6%">&nbsp;</td>
                  <td width="44%"><input name="imageField" type="image" src="img/next.gif" width="52" height="20" border="0" style="border:0;height:20"></td>
                  
              <td width="50%"><a href="file_manage_main.php?activepath=<?=$activepath?>"><img src="img/file_view.gif" width="60" height="20" border="0"></a></td>
                </tr>
              </table></td>
          <td width="22%" bgcolor="#F9F9F7">
          <table width="100%" border="0" cellspacing="1" cellpadding="0">
              <tr> 
                <td width="18%" align="right"><img src="img/file_topdir.gif" width="18" height="17"></td>
                <td width="82%"><? GetPrePath($activepath); ?></td>
              </tr>
            </table></td>
        </tr>
      </table></td>
  </tr>
  <tr><td height="4" colspan="<?=$listSize?>"></td></tr>
  <? ListPic($truePath,$activepath); ?>
  </form>
</table>
</body>
</html>
