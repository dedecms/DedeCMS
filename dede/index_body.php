<?php 
require(dirname(__FILE__)."/config.php");
require(dirname(__FILE__)."/../include/inc_photograph.php");
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>DedeCms Home</title>
<link rel="stylesheet" type="text/css" href="base.css">
<base target="_self">
<style type="text/css">
<!--
.STYLE1 {color: #333333}
.STYLE2 {
	color: #2C73DE;
	font-weight: bold;
}
-->
</style>
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
            -- <?php echo $cfg_softname?>！ 
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
	<table width="100%"  border="0" cellpadding="4" cellspacing="1" bgcolor="#A5D0F1">
        <tr>
          <td colspan="2" background="img/wbg.gif" bgcolor="#E5F9FF">
		  <span class="STYLE2"><b><?php echo $cfg_soft_enname?>  最新消息</b></span></td>
        </tr>
        <tr bgcolor="#FFFFFF">
          <td height="63" colspan="2">
          <table width="100%"  border="0" cellspacing="1" cellpadding="1">
              <form name="uploadspider" action="upload_spider.php" method="post">
                <tr> 
                  <td width="15%" align="center"><img src="img/ico_spider.gif" width="90" height="70"></td>
                  <td><?php echo GetNewInfo()?></td>
                </tr>
              </form>
            </table>
          </td>
        </tr>
      </table>
	  <br/>
	  <table width="100%"  border="0" cellpadding="4" cellspacing="1" bgcolor="#A5D0F1">
        <tr> 
          <td colspan="2" background="img/wbg.gif" bgcolor="#E5F9FF"><span class="STYLE2">◆快捷功能</span></td>
        </tr>
        <tr bgcolor="#FFFFFF"> 
          <td height="30" colspan="2" align="center" valign="bottom"><table width="100%" border="0" cellspacing="1" cellpadding="1">
              <tr>
                <td width="15%" height="31" align="center">
                	<img src="img/qc.gif" width="90" height="30">                </td>
                <td width="85%" valign="bottom"><img src="img/manage1.gif" width="17" height="14"> <a href="catalog_main.php"><u>栏目管理</u></a>&nbsp;<img src="img/manage1.gif" width="17" height="14"> <a href="catalog_menu.php" target="menu"><u>发布文档</u></a>&nbsp;<img src="img/addnews.gif" width="16" height="16">
                	<a href="content_list.php?arcrank=-1"><u>待审核文档</u></a>
                	
                	&nbsp;<img src="img/menuarrow.gif" width="16" height="15">
               	<a href="feedback_main.php"><u>评论管理</u></a>&nbsp;&nbsp;<img src="img/manage1.gif" width="17" height="14"> <a href="sys_info.php"><u>更改系统参数</u></a>&nbsp;<img src="img/part-list.gif" width="16" height="16"> <a href="makehtml_list.php"><u>更新栏目HTML</u></a></td>
              </tr>
            </table></td>
        </tr>
      </table>
	<br/>
	<table width="100%"  border="0" cellpadding="4" cellspacing="1" bgcolor="#A5D0F1">
        <tr bgcolor="#E5F9FF"> 
          <td colspan="2" background="img/wbg.gif"><font color="#666600" class="STYLE2"><b>◆系统基本信息</b></font></td>
        </tr>
        <tr bgcolor="#FFFFFF"> 
          <td width="25%" bgcolor="#FFFFFF">你的级别：</td>
          <td width="75%" bgcolor="#FFFFFF"> 
            <?php 
        if($cuserLogin->getUserType()==10) echo "总管理员";
        else if($cuserLogin->getUserType()==5) echo "频道总编";
        else echo "信息采集员或其它管理员";
        ?>
          </td>
        </tr>
        <tr bgcolor="#FFFFFF"> 
          <td rowspan="5">PHP环境摘要：</td>
          <td> PHP版本： 
            <?php echo @phpversion();?>
            &nbsp;
            GD版本： 
           <?php echo @gdversion()?>
          </td>
        </tr>
        <tr bgcolor="#FFFFFF">
          <td>
          	是否安全模式：<font color='red'><?php echo ($isSafeMode ? 'On' : 'Off')?></font>
          	<?php 
          	if($isSafeMode) echo "<br>　　<font color='blue'>由于你的系统以安全模式运行，为了确保程序兼容性，第一次进入本系统时请更改“<a href='sys_info.php'><u>更改系统参数</u></a>”里的FTP选项，并选择用FTP形式创建目录，完成后：<a href='testenv.php' style='color:red'><u>点击此进行一次DedeCms目录权限检测&gt;&gt;</u></a></font>";
          	else echo "　<a href='testenv.php' style='color:blue'><u>如果你第一次进入本系统，建议点击此进行一次DedeCms目录权限检测&gt;&gt;</u></a></font>";
          	?>
          </td>
        </tr>
        <tr bgcolor="#FFFFFF">
          <td>Register_Globals：<font color='red'> 
            <?php echo ini_get("register_globals") ? 'On' : 'Off'?>
            </font> &nbsp; Magic_Quotes_Gpc：<font color='red'> 
            <?php echo ini_get("magic_quotes_gpc") ? 'On' : 'Off'?>
            </font></td>
        </tr>
        <tr bgcolor="#FFFFFF">
          <td>支持上传的最大文件： 
            <?php echo ini_get("post_max_size")?>
          </td>
        </tr>
        <tr bgcolor="#FFFFFF">
          <td>是否允许打开远程连接： 
            <?php echo ini_get("allow_url_fopen") ? '支持' : '不支持'?>
          </td>
        </tr>
        <tr bgcolor="#FFFFFF"> 
          <td>系统摘要：</td>
          <td> 档案总数：
		  <?php 
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
          	版本名称：<?php echo $cfg_soft_enname?>
          	&nbsp;
          	版本号：<?php echo $cfg_version?>
          </td>
        </tr>
        <tr bgcolor="#FFFFFF"> 
          <td width="25%">开发团队：</td>
          <td width="75%"><?php echo $cfg_soft_devteam?></td>
        </tr>
      </table>
	<br/>
	<table width="100%"  border="0" cellpadding="4" cellspacing="1" bgcolor="#A5D0F1">
        <tr bgcolor="#E5F9FF"> 
          <td colspan="2" background="img/wbg.gif"><b class="STYLE2">◆使用帮助</b></td>
        </tr>
        <tr bgcolor="#FFFFFF">
          <td height="43">官方论坛：</td>
          <td><a href="http://bbs.dedecms.com/" target="_blank"><u>http://bbs.dedecms.com</u></a></td>
        </tr>
        <tr bgcolor="#FFFFFF"> 
          <td width="25%" height="43">DedeCms模板标记参考：</td>
          <td width="75%"><a href="http://www.dedecms.com/archives/templethelp/help/index.htm" target="_blank"><u>http://www.dedecms.com/archives/templethelp/help/index.htm</u></a></td>
        </tr>
      </table>
    </td>
  </tr>
  <tr> 
    <td width="100%" height="2" valign="top"></td>
  </tr>
</table>
<p align="center">
<?php echo $cfg_powerby?>
<br/><br/>
</p>
</body>

</html>