<?
require("config.php");
?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=gb2312'>
<title>空白窗体</title>
<link href='base.css' rel='stylesheet' type='text/css'>
</head>
<body background='img/allbg.gif' leftmargin='8' topmargin='8'>
<table width="98%" border="0" cellpadding="2" cellspacing="1" bgcolor="#666666" align="center">
  <tr>
    <td height="23" background="img/tbg.gif"> &nbsp;<b>欢迎使用Dede内容管理系统2.0版系统安装参数</b></td>
</tr>
<tr>
    <td height="320" align="center" valign="top"  bgcolor="#FFFFFF"><table width="98%"  border="0" cellspacing="0" cellpadding="4">
      <tr>
        <td height="30" colspan="2">　　如果你的系统安装后无法正常运行，请查看系统参数是否正确，如果不正确，请在config_base.php中手动更改，如果你依然无法正常使用本系统，咨询时请将这些参数发送给我们，以便诊断问题产生原因。</td>
        </tr>
      <tr bgcolor="#F8F7F5">
        <td height="24">网站根路径($base_dir)：</td>
        <td><?=$base_dir?></td>
      </tr>
      <tr>
        <td height="24">主站网址($base_url)：</td>
        <td><?=$base_url?></td>
      </tr>
      <tr bgcolor="#F8F7F5">
        <td width="41%" height="24">主站名称($webname)：</td>
        <td width="59%"><?=$webname?></td>
      </tr>
      <tr>
        <td height="24">文章存放根目录($art_dir)：</td>
        <td><?=$art_dir?></td>
      </tr>
      <tr>
        <td height="24" colspan="2">空白表示为根目录，所有表示目录的参数，结束均不能带“/”；</td>
        </tr>
      <tr bgcolor="#F8F7F5">
        <td height="24">程序存放根目录($art_php_dir)：</td>
        <td width="59%"><?=$art_php_dir?></td>
      </tr>
      <tr>
        <td height="24">图片浏览器根目录($imgview_dir)：</td>
        <td width="59%">          <?=$imgview_dir?></td>
      </tr>
      <tr bgcolor="#F8F7F5">
        <td height="24">上传的大图片的路径($img_dir)：</td>
        <td width="59%"><?=$img_dir?></td>
      </tr>
      <tr>
        <td height="24">缩略图存放的路径($ddimg_dir)：</td>
        <td width="59%"><?=$ddimg_dir?></td>
      </tr>
      <tr bgcolor="#F8F7F5">
        <td height="24">上传的软件目录($soft_dir)：</td>
        <td width="59%"><?=$soft_dir?></td>
      </tr>
      <tr>
        <td height="24">模板的存放目录($mod_dir)：</td>
        <td width="59%"><?=$mod_dir?></td>
      </tr>
      <tr bgcolor="#F8F7F5">
        <td height="24">数据备份目录($bak_dir)：</td>
        <td width="59%"><?=$bak_dir?></td>
      </tr>
      <tr>
        <td height="24">新建目录的权限($dir_purview)：</td>
        <td><?="0".decoct($dir_purview)?></td>
      </tr>
	  <tr bgcolor="#F8F7F5">
        <td height="24">管理员的Email($admin_email)：</td>
        <td width="59%"><?=$admin_email?></td>
      </tr>
      <tr>
        <td height="24">生成文件的扩展名($art_shortname)：</td>
        <td width="59%"><?=$art_shortname?></td>
      </tr>
      <tr bgcolor="#F8F7F5">
        <td height="24">标记风格：</td>
        <td width="59%"><?=$tag_start_char."dede:tagname/".$tag_end_char?>&nbsp;</td>
      </tr>
      <tr>
        <td height="24">数据服务器：</td>
        <td><?=$dbhost?></td>
      </tr>
	  <tr bgcolor="#F8F7F5">
        <td height="24">数据库：</td>
        <td width="59%"><?=$dbname?></td>
      </tr>
      <tr>
        <td height="24">数据库用户名：</td>
        <td width="59%"><?=$dbusername?></td>
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
        <td height="24">文章保存位置($art_nametag)：</td>
        <td><?=$art_nametag?></td>
      </tr>
      <tr>
        <td height="24" colspan="2">//[1] listdir 表示在类目的目录下以 ID.
          <?=$art_shortname?> 
          的形式生成文件<br>
          //[2] maketime 表示以 $artdir/year/monthday/ID.
          <?=$art_shortname?> 
          来生成文件</td>
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
<a href="http://www.dedecms.com" target="_blank">Power by PHP+MySQL 织梦之旅 2004-2006 官方网站：www.DedeCMS.com</a>
<br>
免责声明：任何人不得把本系统进行二次开发后作为商业代码出售，<br>
我们保留对二次开发并已作为开源项目发布的版本的任意形式的使用权利。
</center>
</body>
</html>