<?
require_once(dirname(__FILE__)."/config.php");
?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=gb2312'>
<title>系统配置参数</title>
<link href='base.css' rel='stylesheet' type='text/css'>
</head>
<body background='img/allbg.gif' leftmargin='8' topmargin='8'>
<table width="98%" border="0" cellpadding="2" cellspacing="1" bgcolor="#666666" align="center">
  <tr>
    <td height="23" background="img/tbg.gif"> &nbsp;<b>DedeCms系统安装信息：</b></td>
</tr>
<tr>
    <td height="320" align="center" valign="top"  bgcolor="#FFFFFF"><table width="98%"  border="0" cellspacing="0" cellpadding="4">
        <tr> 
          <td height="30" colspan="2">　　如果你的系统安装后无法正常运行，请查看系统参数是否正确，如果不正确，请在config_base.php中手动更改，如果你依然无法正常使用本系统，咨询时请将这些参数发送给我们，以便诊断问题产生原因。</td>
        </tr>
        <tr bgcolor="#F8F7F5"> 
          <td height="24">网站根路径($cfg_basedir)：</td>
          <td> 
            <?=$cfg_basedir?>
          </td>
        </tr>
        <tr> 
          <td height="24">主站网址($cfg_basehost)：</td>
          <td> 
            <?=$cfg_basehost?>
          </td>
        </tr>
        <tr bgcolor="#F8F7F5"> 
          <td width="41%" height="24">主站名称($cfg_webname)：</td>
          <td width="59%"> 
            <?=$cfg_webname?>
          </td>
        </tr>
        <tr> 
          <td height="24">文章存放根目录($cfg_arcdir)：</td>
          <td> 
            <?=$cfg_arcdir?>
          </td>
        </tr>
        <tr> 
          <td height="24" colspan="2">空白表示为根目录，所有表示目录的参数，结束均不能带“/”；</td>
        </tr>
        <tr bgcolor="#F8F7F5"> 
          <td height="24">程序存放根目录($cfg_plus_dir)：</td>
          <td width="59%"> 
            <?=$cfg_plus_dir?>
          </td>
        </tr>
        <tr> 
          <td height="24">图片浏览器根目录($cfg_medias_dir)：</td>
          <td width="59%"> 
            <?=$cfg_medias_dir?>
          </td>
        </tr>
        <tr bgcolor="#F8F7F5"> 
          <td height="24">上传的大图片的路径($cfg_image_dir)：</td>
          <td width="59%"> 
            <?=$cfg_image_dir?>
          </td>
        </tr>
        <tr> 
          <td height="24">缩略图存放的路径($ddcfg_image_dir)：</td>
          <td width="59%"> 
            <?=$ddcfg_image_dir?>
          </td>
        </tr>
        <tr bgcolor="#F8F7F5"> 
          <td height="24">上传的软件目录($cfg_soft_dir)：</td>
          <td width="59%"> 
            <?=$cfg_soft_dir?>
          </td>
        </tr>
        <tr> 
          <td height="24">模板的存放目录($cfg_templets_dir)：</td>
          <td width="59%"> 
            <?=$cfg_templets_dir?>
          </td>
        </tr>
        <tr bgcolor="#F8F7F5"> 
          <td height="24">数据备份目录($cfg_backup_dir)：</td>
          <td width="59%"> 
            <?=$cfg_backup_dir?>
          </td>
        </tr>
        <tr> 
          <td height="24">新建目录的权限($cfg_dir_purview)：</td>
          <td> 
            <?="0".decoct($cfg_dir_purview)?>
          </td>
        </tr>
        <tr bgcolor="#F8F7F5"> 
          <td height="24">管理员的Email($cfg_adminemail)：</td>
          <td width="59%"> 
            <?=$cfg_adminemail?>
          </td>
        </tr>
        <tr> 
          <td height="24">数据服务器：</td>
          <td> 
            <?=$dbhost?>
          </td>
        </tr>
        <tr bgcolor="#F8F7F5"> 
          <td height="24">数据库：</td>
          <td width="59%"> 
            <?=$cfg_dbname?>
          </td>
        </tr>
        <tr> 
          <td height="24">数据库用户名：</td>
          <td width="59%"> 
            <?=$cfg_dbuser?>
          </td>
        </tr>
        <tr bgcolor="#F8F7F5"> 
          <td height="24" bgcolor="#F8F7F5">数据库密码：</td>
          <td width="59%">******</td>
        </tr>
        <tr> 
          <td height="24">&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr bgcolor="#F8F7F5"> 
          <td height="24">&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr> 
          <td height="24">&nbsp;</td>
          <td width="59%">&nbsp;</td>
        </tr>
      </table></td>
</tr>
</table>
<center>
</center>
</body>
</html>