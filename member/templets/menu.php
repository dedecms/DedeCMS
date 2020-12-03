<!-- //个人工具箱 -->
<table width="100%" border="0" cellpadding="0" cellspacing="0" style="margin-bottom:6px">
        <tr align="center"> 
          
    <td height="24" colspan="2" class="mmt1">
	<table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin-top:2px">
        <tr>
          <td width="28%" height="18" align="right" valign="top"><img src="img/dd/dedeexplode.gif" width="11" height="11" class="ittop"></td>
          <td width="72%"><strong>个人工具箱</strong></td>
        </tr>
      </table>
      
    </td>
        </tr>
        <tr bgcolor="#FFFFFF"> 
          <td width="22%" height="21" align="center" class="mml"><img src="img/dd/stow.gif" width="16" height="16"></td>
          <td class="mmr"><a href="mystow.php"><u>我的收藏夹</u></a></td>
        </tr>
        <tr bgcolor="#FFFFFF"> 
          <td width="22%" height="21" align="center" class="mml"><img src="img/dd/cd.gif" width="16" height="16"></td>
          <td class="mmr"><a href="guestbook_admin.php"><u>我的留言簿</u></a></td>
        </tr>
        <tr bgcolor="#FFFFFF"> 
          <td height="21" align="center" class="mml"><img src="img/dd/dir.gif" width="16" height="16"></td>
          <td class="mmr"><a href="mypay.php"><u>金币消费记录</u></a></td>
        </tr>
		   <tr bgcolor="#FFFFFF"> 
          <td height="21" align="center" class="mml"><img src="img/dd/dir.gif" width="16" height="16"></td>
          <td class="mmr"><a href="my_operation.php"><u>历史订单管理</u></a></td>
        </tr>
        <tr> 
          <td colspan="2" height="6" class="mmb">&nbsp;</td>
        </tr>
</table>
<!-- //个人空间 -->
<table width="100%" border="0" cellpadding="0" cellspacing="0" style="margin-bottom:6px">
        <tr align="center"> 
          
    <td height="24" colspan="2" class="mmt"><table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin-top:2px">
        <tr> 
          <td width="28%" height="18" align="right" valign="top"><img src="img/dd/dedeexplode.gif" width="11" height="11" class="ittop"></td>
          <td width="72%"><strong>个人资料</strong></td>
        </tr>
      </table>
    </td>
        </tr>
        <tr bgcolor="#FFFFFF"> 
          <td height="21" align="center" class="mml"><img src="img/dd/menumember.gif" width="16" height="15"></td>
          <td class="mmr"><a href="edit_pwd.php"><u>登录密码更改</u></a></td>
        </tr>
        <tr bgcolor="#FFFFFF"> 
          <td height="21" align="center" class="mml"><img src="img/dd/menumember.gif" width="16" height="15"></td>
          <td class="mmr"><a href="edit_info.php"><u>个人资料更改</u></a></td>
        </tr>
        <tr bgcolor="#FFFFFF"> 
          <td height="21" align="center" class="mml"><img src="img/dd/home.gif" width="16" height="16"></td>
          <td class="mmr"><a href="space_info.php"><u>空间信息更改</u></a></td>
        </tr>
        <tr bgcolor="#FFFFFF"> 
          <td height="21" align="center" class="mml"><img src="img/dd/dir.gif" width="16" height="16"></td>
          <td class="mmr"><a href="flink_main.php"><u>友情链接管理</u></a></td>
        </tr>
        <tr> 
          <td colspan="2" height="6" class="mmb">&nbsp;</td>
        </tr>
</table>
<!-- //文章 -->
<table width="100%" border="0" cellpadding="0" cellspacing="0" style="margin-bottom:6px">
        <tr align="center"> 
          
    <td height="24" colspan="2" class="mmt"><table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin-top:2px">
        <tr> 
          <td width="28%" height="18" align="right" valign="top"><img src="img/dd/dedeexplode.gif" width="11" height="11" class="ittop"></td>
          <td width="72%"><strong>我的文档</strong></td>
        </tr>
      </table>
      
    </td>
        </tr>
        <tr bgcolor="#FFFFFF"> 
          <td height="21" align="center" class="mml"><img src="img/dd/exe.gif" width="16" height="16"></td>
          <td class="mmr"><a href="article_add.php"><u>发表新文章</u></a></td>
        </tr>
        <tr bgcolor="#FFFFFF"> 
          <td width="22%" height="21" align="center" class="mml"><img src="img/dd/dir.gif" width="16" height="16"></td>
          <td class="mmr"><a href="content_list.php?channelid=1"><u>已发表的文章</u></a></td>
        </tr>
    <?php 
    if($cfg_mb_album=='是'){
    ?>    
    <tr bgcolor="#FFFFFF"> 
        <td height="21" align="center" class="mml"><img src="img/dd/exe.gif" width="16" height="16"></td>
        <td class="mmr"><a href="album_add.php"><u>发表新图集</u></a></td>
    </tr>
    <tr bgcolor="#FFFFFF"> 
        <td width="22%" height="21" align="center" class="mml"><img src="img/dd/dir.gif" width="16" height="16"></td>
        <td class="mmr"><a href="content_list.php?channelid=2"><u>已发表的图集</u></a></td>
    </tr>
  <?php  } ?>
     <tr bgcolor="#FFFFFF"> 
        <td height="21" align="center" class="mml"><img src="img/dd/image.gif" width="16" height="16"></td>
        <td class="mmr"><a href="space_upload.php"><u>附件管理</u></a></td>
     </tr>
     <tr bgcolor="#FFFFFF"> 
          <td height="21" align="center" class="mml"><img src="img/dd/dir2.gif" width="16" height="16"></td>
          <td class="mmr"><a href="archives_type.php"><u>管理我的分类</u></a></td>
    </tr>
        <tr> 
          <td colspan="2" height="6" class="mmb">&nbsp;</td>
        </tr>
</table>
<!-- 自定义模型投稿 -->
<?php 
if($cfg_mb_sendall=='是'){
if(!isset($dsql) || !is_object($dsql)){
	$dsql = new DedeSql(false);
}
$dsql->SetQuery("Select ID,typename From #@__channeltype where issend=1 And issystem=0");
$dsql->Execute();
while($menurow = $dsql->GetArray())
{
?>
<table width="100%" border="0" cellpadding="0" cellspacing="0" style="margin-bottom:6px">
        <tr align="center"> 
          
    <td height="24" colspan="2" class="mmt"><table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin-top:2px">
        <tr> 
          <td width="28%" height="18" align="right" valign="top"><img src="img/dd/dedeexplode.gif" width="11" height="11" class="ittop"></td>
          <td width="72%"><strong><?php echo $menurow['typename']?></strong></td>
        </tr>
      </table>
      
    </td>
        </tr>
        <tr bgcolor="#FFFFFF"> 
          <td height="21" align="center" class="mml"><img src="img/dd/exe.gif" width="16" height="16"></td>
          <td class="mmr"><a href="archives_add.php?channelid=<?php echo $menurow['ID']?>"><u>发布新<?php echo $menurow['typename']?></u></a></td>
        </tr>
        <tr bgcolor="#FFFFFF"> 
          <td width="22%" height="21" align="center" class="mml"><img src="img/dd/dir.gif" width="16" height="16"></td>
          <td class="mmr"><a href="content_list.php?channelid=<?php echo $menurow['ID']?>"><u>已发布<?php echo $menurow['typename']?></u></a></td>
        </tr>
        <tr> 
          <td colspan="2" height="6" class="mmb">&nbsp;</td>
        </tr>
</table>
<?php 
}}
?>