<?php 
require(dirname(__FILE__)."/config.php");
require(dirname(__FILE__)."/../include/inc_photograph.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>DedeCms Home</title>
<link href="css_body.css" rel="stylesheet" type="text/css" />
</head>

<body>
<table width="96%" border="0" align="center" cellpadding="0" cellspacing="0">
		<tr>
				<td style="color:#FF6600; line-height:31px;"><strong>欢迎使用国内最专业的PHP网站管理系统,轻松建站的首选利器 -- <?php echo $cfg_softname?></strong></td>
  </tr>
</table>

<div class="bodytitle">
	<div class="bodytitleleft"></div>
	<div class="bodytitletxt">DedeCms最新消息</div>
</div>
<table width="96%" border="0" align="center" cellpadding="0" cellspacing="0">
	<tr><form name="uploadspider" action="upload_spider.php" method="post">
		<td height="80" class="main_dnews">
			<?php echo GetNewInfo()?>		 </td>
         </form>
	</tr>
</table>

<div class="bodytitle">
	<div class="bodytitleleft"></div>
	<div class="bodytitletxt">快捷功能</div>
</div>
<table width="96%" border="0" align="center" cellpadding="0" cellspacing="0">
	<tr>
		<td height="60" align="center">
			<table width="96%" border="0" cellspacing="10" cellpadding="0" style="margin-top:10px;">
				<tr class="main_qbut">
					<td width="16%" align="center"><div style="background-position:center 10px;"><a href="catalog_main.php">栏目管理</a></div></td>
					<td width="16%" align="center"><div style="background-position:center -130px;"><a href="catalog_menu.php" target="menu">发布文档</a></div></td>
					<td width="16%" align="center"><div style="background-position:center -270px;"><a href="content_list.php?arcrank=-1">待审核文档</a></div></td>
					<td width="16%" align="center"><div style="background-position:center -414px;"><a href="feedback_main.php">评论管理 </a></div></td>
					<td width="16%" align="center"><div style="background-position:center -554px;"><a href="makehtml_list.php">更新HTML</a></div></td>
					<td width="16%" align="center"><div style="background-position:center -694px;"><a href="sys_info.php">更改系统参数 </a></div></td>
					<td width="4%" align="center">&nbsp;</td>
				</tr>
			</table>
		</td>
	</tr>
</table>


<div class="bodytitle">
	<div class="bodytitleleft"></div>
	<div class="bodytitletxt">系统基本信息</div>
</div>
<table width="96%" border="0" align="center" cellpadding="10" cellspacing="1" bgcolor="#E2F5BC" style="margin-top:6px;">
		<tr>
				<td align="right" bgcolor="#F9FFE6" class="main_bleft">你的级别：</td>
				<td bgcolor="#FFFFFF" class="main_bright"><strong>
         <?php 
                if($cuserLogin->getUserType()==10) echo "总管理员";
                else if($cuserLogin->getUserType()==5) echo "频道总编";
                else echo "信息采集员或其它管理员";
        	?></strong></td>
		</tr>
		<tr>
				<td width="22%" rowspan="5" align="right" bgcolor="#F9FFE6" class="main_bleft">PHP环境摘要：</td>
				<td bgcolor="#FFFFFF" class="main_bright"><strong>PHP版本：</strong> <?php echo @phpversion();?>   <strong>GD版本：</strong> <?php echo @gdversion()?> </td>
		</tr>
		<tr>
				<td bgcolor="#FFFFFF" class="main_bright"><strong>是否安全模式：</strong>  <?php echo ($cfg_isSafeMode ? 'On' : 'Off')?> <?php 
          	if($cfg_isSafeMode) echo "<br>　　<font color='blue'>由于你的系统以安全模式运行，为了确保程序兼容性，第一次进入本系统时请更改“<a href='sys_info.php'><u>更改系统参数</u></a>”里的FTP选项，并选择用FTP形式创建目录，完成后：<a href='testenv.php' style='color:red'><u>点击此进行一次DedeCms目录权限检测&gt;&gt;</u></a></font>";
          	else echo "　<a href='testenv.php' style='color:blue'><u>如果你第一次进入本系统，建议点击此进行一次DedeCms目录权限检测&gt;&gt;</u></a></font>";
          	?></td>
		</tr>
		<tr>
				<td bgcolor="#FFFFFF" class="main_bright"><strong>Register_Globals：</strong>  <?php echo ini_get("register_globals") ? 'On' : 'Off'?>   <strong>Magic_Quotes_Gpc：</strong> <?php echo ini_get("magic_quotes_gpc") ? 'On' : 'Off'?> </td>
		</tr>
		<tr>
				<td width="78%" bgcolor="#FFFFFF" class="main_bright"><strong>支持上传的最大文件：</strong> <?php echo ini_get("post_max_size")?> </td>
		</tr>
		<tr>
				<td bgcolor="#FFFFFF" class="main_bright"><strong>是否允许打开远程连接：</strong> <?php echo ini_get("allow_url_fopen") ? '支持' : '不支持'?> </td>
		</tr>
		
		<tr>
				<td align="right" bgcolor="#F9FFE6" class="main_bleft">系统摘要：</td>
				<td bgcolor="#FFFFFF" class="main_bright"><strong>档案总数：</strong> 
                <?php 
                      $dsql = new DedeSql(false);
                      $row = $dsql->GetOne("Select count(ID) as cc From #@__archives");
                      $dsql->Close();
                      echo $row['cc'];
		  		?> 
                </td>
		</tr>
		<tr>
				<td align="right" bgcolor="#F9FFE6" class="main_bleft">软件版本信息：</td>
				<td bgcolor="#FFFFFF" class="main_bright"><strong>版本名称：</strong><?php echo $cfg_soft_enname?><strong> 版本号：</strong><?php echo $cfg_version?></td>
  </tr>
		<tr>
				<td align="right" bgcolor="#F9FFE6" class="main_bleft">开发团队：</td>
				<td bgcolor="#FFFFFF" class="main_bright"><?php echo $cfg_soft_devteam?></td>
  </tr>
</table>

<div class="bodytitle">
	<div class="bodytitleleft"></div>
	<div class="bodytitletxt">使用帮助</div>
</div>
<table width="96%" border="0" align="center" cellpadding="10" cellspacing="1" bgcolor="#E2F5BC" style="margin-top:6px;">
		<tr>
				<td width="22%" align="right" bgcolor="#F9FFE6" class="main_bleft">官方论坛：</td>
				<td width="78%" bgcolor="#FFFFFF" class="main_bright"><a href="http://bbs.dedecms.com">http://bbs.dedecms.com </a></td>
		</tr>
		<tr>
				<td align="right" bgcolor="#F9FFE6" class="main_bleft">模板标记参考：</td>
				<td bgcolor="#FFFFFF" class="main_bright"><a href="http://www.dedecms.com/archives/templethelp/help/index.htm">http://www.dedecms.com/archives/templethelp/help/index.htm</a></td>
		</tr>
</table>

<table width="96%" border="0" align="center" cellpadding="0" cellspacing="0">
		<tr>
				<td align="center" style="line-height:51px;"><?php echo $cfg_powerby?><br /></td>
  </tr>
</table>
</body>
</html>
<?php ClearAllLink(); ?>