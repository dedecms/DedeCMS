<?
require("config.php");
if(!isset($activepath)) $activepath="";
$inpath = "";
?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=gb2312'>
<title>文件管理器</title>
<link href='base.css' rel='stylesheet' type='text/css'>
<script src='menu.js' language='JavaScript'></script>
</head>
<body background='img/allbg.gif' leftmargin='0' topmargin='0'>
<table width="98%" border="0" align="center">
  <tr>
    <td><table width='100%' border='0' cellspacing='0' cellpadding='0'>
        <tr> 
          <td height='4' colspan='4'></td>
        </tr>
        <tr> 
          <td colspan='4' align='right'> <table width='100%' border='0' cellpadding='0' cellspacing='1' bgcolor='#666666'>
              <tr bgcolor='#FFFFFF'> 
                <td colspan='4'> <table width='100%' border='0' cellspacing='0' cellpadding='2'>
                    <tr bgcolor="#CCCCCC"> 
                      <td width="178" align="center" background="img/tbgv.gif" bgcolor="#98C6EF" class='linerow'><strong>文件名</strong></td>
                      <td width="120" align="center" background="img/tbg.gif" bgcolor="#DDF1F9" class='linerow'><strong>文件大小</strong></td>
                      <td width="135" align="center" bgcolor='#98C6EF' class='linerow' background="img/tbgv.gif"><strong>最后修改时间</strong></td>
                      <td width="180" align="center" background="img/tbg.gif" bgcolor="#DDF1F9" class='linerow'><strong>操作</strong></td>
                    </tr>
                    <?
$activepath = ereg_replace("^/{1,}","/",$activepath);                   
if($activepath == "") $inpath = $base_dir;
else $inpath = $base_dir.$activepath; #注 $activepath 用 "/" 表示根目录 如/news 但根目录本身不用
$dh = dir($inpath);
$ty1="";
$ty2="";
while($file = $dh->read()) {
     $filesize = @filesize("$inpath/$file");
     $filesize=$filesize/1024;
     if($filesize!="")
     if($filesize<0.1)
     {
         @list($ty1,$ty2)=split("\.",$filesize);
         $filesize=$ty1.".".substr($ty2,0,2);
     }
     else
     {
          @list($ty1,$ty2)=split("\.",$filesize);
          $filesize=$ty1.".".substr($ty2,0,1);
     }
     $filetime = @filemtime("$inpath/$file");
     $filetime = @strftime("%y-%m-%d %H:%M:%S",$filetime);
     if($file == ".") continue;
     else if($file == ".."){
            if($activepath == "") continue;
            $tmp = eregi_replace("[/][^/]*$","",$activepath);
            $line = "\n<tr>
            <td class='linerow'> <a href=file_view.php?activepath=$tmp>上级目录<img src=img/dir2.gif border=0 width=16 height=13></a></td>
            <td colspan='3' class='linerow'> 当前目录:$activepath &nbsp;<a href='file_pic.php?activepath=$activepath' style='color:red'>[图片浏览器]</a></td>
            </tr>";
            echo $line;
      }
      else if(is_dir("$inpath/$file")){
             if(eregi("^_(.*)$",$file)) continue; #屏蔽FrontPage扩展目录和linux隐蔽目录
             if(eregi("^\.(.*)$",$file)) continue;
             $line = "\n<tr>
             <td bgcolor='#F5F5F5' class='linerow'> <a href=file_view.php?activepath=$activepath/$file><img src=img/dir.gif border=0 width=16 height=13>$file</a></td>
             <td class='linerow'>　</td>
             <td bgcolor='#F5F5F5' class='linerow'>　</td>
             <td class='linerow'><a href=file_rename.php?filename=$file&activepath=$activepath>[改名]</a>&nbsp;<a href=file_del.php?filename=$file&activepath=$activepath&type=dir>[删除]</a></td>
             </td>
             </tr>";
             echo "$line";
      }
      else if(eregi("\.(jpg|gif|png)",$file)){
             $line = "\n<tr>
             <td bgcolor='#F5F5F5' class='linerow'><a href=$activepath/$file target=_blank><img src=img/img.gif border=0 width=16 height=13>$file</a></td>
             <td class='linerow'>$filesize KB</td>
             <td align='center' bgcolor='#F5F5F5' class='linerow'>$filetime</td>
             <td class='linerow'><a href=file_rename.php?filename=$file&activepath=$activepath>[改名]</a>&nbsp;<a href=file_del.php?filename=$file&activepath=$activepath>[删除]</a>&nbsp;<a href=file_move.php?filename=$file&activepath=$activepath>[移动]</a></td>
             </tr>";
             echo "$line";
     }
     else if(eregi("\.(htm|txt|inc|php|pl|cgi|css|asp|jsp|xml|js|xsl|aspx|cfm)",$file))
     {
             $line = "\n<tr>
             <td bgcolor='#F5F5F5' class='linerow'><a href=$activepath/$file target=_blank><img src=img/txt.gif border=0 width=16 height=13>$file</a></td>
             <td class='linerow'>$filesize KB</td>
             <td align='center' bgcolor='#F5F5F5' class='linerow'>$filetime</td>
             <td class='linerow'><a href=file_edit.php?activepath=$activepath&filename=$file&job=edit>[编辑]</a>&nbsp;<a href=file_rename.php?filename=$file&activepath=$activepath>[改名]</a>&nbsp;<a href=file_del.php?filename=$file&activepath=$activepath>[删除]</a>&nbsp;<a href=file_move.php?filename=$file&activepath=$activepath>[移动]</a></td>
             </tr>";
             echo "$line";
     }
     else
     {
             $line = "\n<tr>
              <td bgcolor='#F5F5F5' class='linerow'><a href=$activepath/$file target=_blank>$file</td>
              <td class='linerow'>$filesize KB</td>
              <td align='center' bgcolor='#F5F5F5' class='linerow'>$filetime</td>
              <td class='linerow'><a href=file_rename.php?filename=$file&activepath=$activepath>[改名]</a>&nbsp;<a href=file_del.php?filename=$file&activepath=$activepath>[删除]</a>&nbsp;<a href=file_move.php?filename=$file&activepath=$activepath>[移动]</a></td>
              </tr>";
              echo "$line";
     }
}
$dh->close();
?>
                    <tr> 
                      <td colspan="4" background="img/tbg.gif"><a href='file_view.php'>[根目录]</a>&nbsp;<a href=file_edit.php?activepath=<? echo $activepath ?>&filename=<? echo $file ?>&job=new>[新建文件]</a>&nbsp;<a href=file_newdir.php?activepath=<? echo $activepath ?>>[新建目录]</a>&nbsp;<a href=file_upload.php?activepath=<? echo $activepath ?>>[文件上传]</a>&nbsp;<a href=file_checkspace.php?activepath=<? echo $activepath ?>>[空间检查]</a>&nbsp;&nbsp;</td>
                    </tr>
                  </table></td>
              </tr>
            </table></td>
        </tr>
      </table></td>
  </tr>
</table>
</body>

</html>
