<?php
$add_channel_menu = array();
//如果为游客访问，不启用左侧菜单
if(!empty($cfg_ml->M_ID))
{
	$channelInfos = array();
	$dsql->Execute('addmod',"SELECT id,nid,typename,useraddcon,usermancon,issend,issystem,usertype,isshow FROM `#@__channeltype` ");	
	while($menurow = $dsql->GetArray('addmod'))
	{
		$channelInfos[$menurow['nid']] = $menurow;
		//禁用的模型
		if($menurow['isshow']==0)
		{
			continue;
		}
		//其它情况
		if($menurow['issend']!=1 || $menurow['issystem']==1 
		|| ( !ereg($cfg_ml->M_MbType, $menurow['usertype']) && trim($menurow['usertype'])!='' ) )
		{
			continue;
		}
		$menurow['ddcon'] = empty($menurow['useraddcon']) ? 'archives_add.php' : $menurow['useraddcon'];
		$menurow['list'] = empty($menurow['usermancon']) ? 'content_list.php' : $menurow['usermancon'];
		$add_channel_menu[] = $menurow;
	}
	unset($menurow);
?>
<script language='javascript'>
	function ShowHideMenuD(mid)
	{
		if($("#"+mid).css("display") == 'block') {
			$("#"+mid).hide(200);
		}
		else {
			$("#"+mid).show(200);
		}
	}
</script>
<div class="dedeLeft">
   <div class='allmenu'>
    <!-- //内容管理 -->
    <div class='menuTitle mbccontent' onclick="ShowHideMenuD('mbccontent')"></div>
    <ul class="leftNav" id="mbccontent">
        <?php
        //是否启用文章投稿
        if($channelInfos['article']['issend']==1 && $channelInfos['article']['isshow']==1)
        {
        ?>
        <li class="icon article"><a href="../member/content_list.php?channelid=1" title="已发布的文章">文章</a><em class="black"><a href="../member/article_add.php" title="发表新文章">发表&raquo;</a></em></li>
        <?php
      	}
        //是否启用图集投稿
        if($channelInfos['image']['issend']==1 && $cfg_mb_album=='Y'  && $channelInfos['image']['isshow']==1 
        && ($channelInfos['image']['usertype']=='' || ereg($cfg_ml->fields['mtype'], $channelInfos['image']['usertype'])) )
        {
        ?>
        <li class="icon image"><a href="../member/content_list.php?channelid=2" title="管理图集">图集</a><em class="black"><a href="../member/album_add.php" title="新建图集">新建&raquo;</a></em></li>
        <?php
      	}
      	//是否启用软件投稿
        if($channelInfos['soft']['issend']==1 && $channelInfos['soft']['isshow']==1
        && ($channelInfos['image']['usertype']=='' || ereg($cfg_ml->fields['mtype'], $channelInfos['image']['usertype']))
        )
        {
        ?>
        <li class="icon soft"><a href="../member/content_list.php?channelid=3" title="已发布的软件">软件</a><em class="black"><a href="../member/soft_add.php" title="上传软件">上传&raquo;</a></em></li>
       <?php
     		}
//是否允许对自定义模型投稿
if($cfg_mb_sendall=='Y')
{
       foreach($add_channel_menu as $nnarr) {
       ?>
        <li class="icon channel">
        <a href="../member/<?php echo $nnarr['list'];?>?channelid=<?php echo $nnarr['id'];?>" title="已发布的<?php echo $nnarr['typename'];?>"><?php echo $nnarr['typename'];?></a>
        </li>
<?php
} }
?>
    </ul>
    <!-- //资料设置 -->
    <div class='menuTitle mbcconfig' onclick="ShowHideMenuD('mbcconfig')"></div>
    <ul class="leftNav" id="mbcconfig">
        <li class="icon myinfo"><a href="../member/edit_fullinfo.php"><?php echo $cfg_ml->M_MbType; ?>资料</a></li>
        <li class="icon myconfig"><a href="../member/mtypes.php">空间管理</a></li>
    </ul>
    <!-- //消费管理 -->
    <div class='menuTitle mbcmoney' onclick="ShowHideMenuD('mbcmoney')"></div>
    <ul class="leftNav" id="mbcmoney">
        <li class="icon consume1"><a href="../member/operation.php">财务管理</a></li>
        <li class="icon consume"><a href="../member/buy.php">升级/充值</a></li>
    </ul>
    <!-- //应用管理 -->
  	<div class='menuTitle mbcapp' onclick="ShowHideMenuD('mbcapp')"></div>
    <ul class="leftNav" id="mbcapp">
        <?php
        if($cfg_feedback_forbid=='N')
        {
          //<li class="icon feedback"><a href='../member/myfeedback.php'>我的评论</a></li>
        }
        $dsql->Execute('nn','Select indexname,indexurl From `#@__sys_module` where ismember=1 ');
        while($nnarr = $dsql->GetArray('nn'))
        {
        	@preg_match("/\/(.+?)\//is", $nnarr['indexurl'],$matches);
        	$nnarr['class'] = isset($matches[1]) ? $matches[1] : 'channel';
        ?>
        <li class="icon <?php echo $nnarr['class'];?>"><a href="<?php echo $nnarr['indexurl']; ?>"><?php echo $nnarr['indexname']; ?>模块</a></li>
        <?php
        }
        ?>
    </ul>
    <hr width="94%" class="dotted" />
    <button class="button5 mTB10" onclick="location='../member/edit_space_info.php';">空间设置</button>
    <hr width="94%" class="dotted" />
    <!--div class="lineB"></div-->
  </div>
</div>
<?php
}
?>