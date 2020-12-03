<?
require(dirname(__FILE__)."/config.php");
require(dirname(__FILE__)."/../include/inc_photograph.php");
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>DedeCms Home</title>
<link rel="stylesheet" type="text/css" href="base.css">
<base target="_self">
</head>
<body leftmargin="8" topmargin='8'>
<table width="98%" border="0" align="center" cellpadding="0" cellspacing="0" bordercolor="#111111" style="BORDER-COLLAPSE: collapse">
  <tr> 
    <td width="100%" height="20" valign="top">
    	<table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr> 
          <td height="30">
          	<IMG height=14 src="img/book1.gif" width=20>
          	 &nbsp;欢迎使用中国最强大的开源网站内容管理项目 
            -- <?=$cfg_softname?>！ 
           </td>
        </tr>
      </table>
      </td>
  </tr>
  <tr> 
    <td width="100%" height="1" background="img/sp_bg.gif"></td>
  </tr>
  <tr> 
    <td width="100%" height="4"></td>
  </tr>
  <tr> 
    <td width="100%" height="20">
	<table width="100%"  border="0" cellpadding="4" cellspacing="1" bgcolor="#CBD8AC">
        <tr>
          <td colspan="2" background="img/wbg.gif" bgcolor="#EEF4EA"><font color="#666600"><b><?=$cfg_soft_enname?> 最新消息</b></font></td>
        </tr>
        <tr bgcolor="#FFFFFF">
          <td height="63" colspan="2">
          <table width="100%"  border="0" cellspacing="1" cellpadding="1">
              <form name="uploadspider" action="upload_spider.php" method="post">
                <tr> 
                  <td width="15%" align="center"><img src="img/ico_spider.gif" width="90" height="70"></td>
                  <td><?=GetNewInfo()?></td>
                </tr>
              </form>
            </table>
          </td>
        </tr>
      </table>
	  <br/>
	  <table width="100%"  border="0" cellpadding="4" cellspacing="1" bgcolor="#CBD8AC">
        <tr> 
          <td colspan="2" background="img/wbg.gif" bgcolor="#EEF4EA"><font color="#666600"><b>快捷功能</b></font></td>
        </tr>
        <tr bgcolor="#FFFFFF"> 
          <td height="30" colspan="2" align="center" valign="bottom"><table width="100%" border="0" cellspacing="1" cellpadding="1">
              <tr>
                <td width="15%" height="31" align="center">
                	<img src="img/qc.gif" width="90" height="30">
                </td>
                <td width="85%" valign="bottom">
                	<img src="img/manage1.gif" width="17" height="14">
                	<a href="sys_info.php"><u>更改系统参数</u></a>
                	
                	<img src="img/part-index.gif" width="16" height="16">
                	<a href="makehtml_homepage.php"><u>更新主页HTML</u></a>
                	
                	<img src="img/part-list.gif" width="16" height="16">
                	<a href="makehtml_list.php"><u>更新指定栏目的HTML</u></a>
                	
                	<img src="img/addnews.gif" width="16" height="16">
                	<a href="content_list.php"><u>文档列表</u></a>
                	
                	<img src="img/menuarrow.gif" width="16" height="15">
                	<a href="feedback_main.php"><u>评论管理</u></a>
                	
                	<img src="img/manage1.gif" width="17" height="14">
                	<a href="catalog_main.php"><u>内容发布</u></a>
                </td>
              </tr>
            </table></td>
        </tr>
      </table>
	<br/>
	<table width="100%"  border="0" cellpadding="4" cellspacing="1" bgcolor="#CBD8AC">
        <tr bgcolor="#EEF4EA"> 
          <td colspan="2" background="img/wbg.gif"><font color="#666600"><b>系统基本信息</b></font></td>
        </tr>
        <tr bgcolor="#FFFFFF"> 
          <td width="25%" bgcolor="#FFFFFF">你的级别：</td>
          <td width="75%" bgcolor="#FFFFFF"> 
            <?
        if($cuserLogin->getUserType()==10) echo "总管理员";
        else if($cuserLogin->getUserType()==5) echo "频道总编";
        else echo "信息采集员或其它管理员";
        ?>
          </td>
        </tr>
        <tr bgcolor="#FFFFFF"> 
          <td rowspan="5">PHP环境摘要：</td>
          <td> PHP版本： 
            <?=@phpversion();?>
            &nbsp;
            GD版本： 
           <?=@gdversion()?>
           </td>
        </tr>
        <tr bgcolor="#FFFFFF">
          <td>
          	是否安全模式：<font color='red'><?=($isSafeMode ? 'On' : 'Off')?></font>
          	<?
          	if($isSafeMode) echo "<br>　　<font color='blue'>由于你的系统以安全模式运行，为了确保程序兼容性，第一次进入本系统时请更改“<a href='sys_info.php'><u>更改系统参数</u></a>”里的FTP选项，并选择用FTP形式创建目录，完成后：<a href='testenv.php' style='color:red'><u>点击此进行一次DedeCms目录权限检测&gt;&gt;</u></a></font>";
          	else echo "　<a href='testenv.php' style='color:blue'><u>如果你第一次进入本系统，建议点击此进行一次DedeCms目录权限检测&gt;&gt;</u></a></font>";
          	?>
          </td>
        </tr>
        <tr bgcolor="#FFFFFF">
          <td>Register_Globals：<font color='red'> 
            <?=ini_get("register_globals") ? 'On' : 'Off'?>
            </font> &nbsp; Magic_Quotes_Gpc：<font color='red'> 
            <?=ini_get("magic_quotes_gpc") ? 'On' : 'Off'?>
            </font></td>
        </tr>
        <tr bgcolor="#FFFFFF">
          <td>支持上传的最大文件： 
            <?=ini_get("post_max_size")?>
          </td>
        </tr>
        <tr bgcolor="#FFFFFF">
          <td>是否允许打开远程连接： 
            <?=ini_get("allow_url_fopen") ? '支持' : '不支持'?>
          </td>
        </tr>
        <tr bgcolor="#FFFFFF"> 
          <td>系统摘要：</td>
          <td> 档案总数：
		  <?
		  $dsql = new DedeSql(false);
		  $row = $dsql->GetOne("Select count(ID) as cc From #@__archives");
		  $dsql->Close();
		  echo $row['cc'];
		  ?>
		  </td>
        </tr>
        <tr bgcolor="#FFFFFF"> 
          <td>软件版本信息：</td>
          <td>
          	版本名称：<?=$cfg_soft_enname?>
          	&nbsp;
          	版本号：<?=$cfg_version?>
          </td>
        </tr>
        <tr bgcolor="#FFFFFF"> 
          <td width="25%">开发团队：</td>
          <td width="75%"><?=$cfg_soft_devteam?></td>
        </tr>
      </table>
	<br/>
	<table width="100%"  border="0" cellpadding="4" cellspacing="1" bgcolor="#CBD8AC">
        <tr bgcolor="#EEF4EA"> 
          <td colspan="2" background="img/wbg.gif"><b><font color="#666600">使用帮助</font></b></td>
        </tr>
        <tr bgcolor="#FFFFFF">
          <td height="43">官方论坛：</td>
          <td><a href="http://bbs.dedecms.com/" target="_blank"><u>http://bbs.dedecms.com</u></a></td>
        </tr>
        <tr bgcolor="#FFFFFF"> 
          <td width="25%" height="43">DedeCms模板标记参考：</td>
          <td width="75%"> <a href="help_templet.php#1"><u>解析引擎概述</u></a> 　<a href="help_templet.php#2"><u>模板设计规范</u></a> 
            　<a href="help_templet.php#3"><u>代码参考</u></a><br/>
            标记参考： <a href="help_templet.php#31"><u>arclist(artlist,likeart,hotart,imglist,imginfolist,coolart,specart)</u></a> 
            &nbsp;<a href="help_templet.php#32"><u>field</u></a> &nbsp;<a href="help_templet.php#33"><u>channel</u></a> 
            &nbsp;<a href="help_templet.php#34"><u>mytag</u></a> &nbsp;<a href="help_templet.php#35"><u>vote</u></a> 
            &nbsp;<a href="help_templet.php#36"><u>friendlink</u></a> &nbsp;<a href="help_templet.php#37"><u>mynews</u></a> 
            &nbsp;<a href="help_templet.php#38"><u>loop</u></a> &nbsp;<a href="help_templet.php#39"><u>channelartlist</u></a> 
            &nbsp;<a href="help_templet.php#310"><u>page</u></a> &nbsp;<a href="help_templet.php#311"><u>list</u></a> 
            &nbsp;<a href="help_templet.php#312"><u>pagelist</u></a> </td>
        </tr>
      </table>
      </td>
  </tr>
  <tr> 
    <td width="100%" height="2" valign="top"></td>
  </tr>
</table>
<p align="center">
<?=$cfg_powerby?>
<br/><br/>
</p>
</body>

</html>