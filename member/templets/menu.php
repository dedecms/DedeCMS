<?php
if($cfg_ml->M_utype == 1){
	 require_once(dirname(__FILE__)."/commenu.php");
}else
{
?>

<script src="<?php echo $cfg_member_dir;?>/jquery.js" language="javascript" type="text/javascript"></script>
<script language="javascript" type="text/ecmascript">
<!--
	$(document).ready(function(){
		smenu = $(".manage_company_title .manage_company_title_bg");
		i=1;
		cname = "dushow";

		smenu.each(function(){
			$(this).attr("_deshow",i);
			mbox = $(this).next(".manage_company_main_text");
			cbname = cname+i;
			if($.cookie(cbname)==null){
				$.cookie(cbname,mbox.css("display"));
			}else{
				$.cookie(cbname) == "none" ? mbox.hide("fast") : mbox.show("fast");
			}
			i++;
		});

		smenu.click(function(){
			mbox = $(this).next(".manage_company_main_text");
			id = $(this).attr("_deshow");
			cbname = cname+id;
			if(mbox.css("display")=="block"){
				mbox.hide("fast");
				$.cookie(cbname,"none");
			}else{
				mbox.show("fast");
				$.cookie(cbname,"block");
			}
		});
	});


-->
</script>

<!-- //个人工具箱 -->
<div id="manage_company_left">
        	<div class="manage_company_title">
                <div class="manage_company_title_bg">个人工具箱</div>
                <div class="manage_company_main_text">
                    <ul>
                    <li><a href="money2score.php">金币、积分兑换</a></li>
                    <li><a href="mystow.php">我的收藏夹</a></li>
                	<li><a href="guestbook_admin.php">我的留言簿</a></li>
                	<li><a href="mypay.php">金币消费记录</a></li>
                    <li><a href="my_operation.php">历史订单管理</a></li>
                    <li><a href="my_friends.php">管理我的好友录</a></li>
                    </ul>
                </div>
            </div>

            <div class="manage_company_title">
                <div class="manage_company_title_bg">个人资料</div>
                <div class="manage_company_main_text">
                    <ul>
                    <li><a href="edit_pwd.php">登录密码更改</a></li>
                	<li><a href="edit_info.php">个人资料更改</a></li>
                	<li><a href="space_info.php">空间信息更改</a></li>
                    <li><a href="flink_main.php">友情链接管理</a></li>
                    </ul>
                </div>
            </div>

            <div class="manage_company_title">
                <div class="manage_company_title_bg">我的短信</div>
                <div class="manage_company_main_text">
                    <ul>
                    <li><a href="pm.php?action=send">发短信</a></li>
                	<li><a href="pm.php?folder=track">发件箱</a></li>
                	<li><a href="pm.php?folder=inbox">收件箱</a></li>
                    <li><a href="pm.php?folder=outbox">草稿箱</a></li>
                    </ul>
                </div>
            </div>

             <!-- add -->

            <div class="manage_company_title">
                <div class="manage_company_title_bg">我的文档</div>
                <div class="manage_company_main_text">
                    <ul>
                    <li><a href="article_add.php">发表新文章</a></li>
                	<li><a href="content_list.php?channelid=1">已发表的文章</a></li>
    <?php
    if($cfg_mb_album=='Y'){
    ?>
                    <li><a href="album_add.php">发表新图集</a></li>
                    <li><a href="content_list.php?channelid=2">已发表的图集</a></li>
                     <?php  } ?>
                    <li><a href="space_upload.php">我上传的文件</a></li>
                    <li><a href="archives_type.php">管理我的分类</a></li>
                    </ul>
                </div>
            </div>

            <div class="manage_company_title">
                <div class="manage_company_title_bg">信息发布</div>
                <div class="manage_company_main_text">
                    <ul>
                    <li><a href="do.php?action=add&channelid=-2">分类信息发布</a></li>
                	<li><a href="do.php?action=list&channelid=-2">分类信息管理</a></li>
                    </ul>
                </div>
            </div>

<?php
if($cfg_mb_sendall=='Y'){
if(!isset($dsql) || !is_object($dsql)){
	$dsql = new DedeSql(false);
}
$dsql->SetQuery("Select ID,typename,useraddcon,usermancon From #@__channeltype where issend=1 And issystem=0 And sendmember<>1 ");
$dsql->Execute();
while($menurow = $dsql->GetArray())
{
	if(empty($menurow['useraddcon'])) $menurow['useraddcon'] = 'archives_add.php';
	if(empty($menurow['usermancon'])) $menurow['usermancon'] = 'content_list.php';
?>
<div class="manage_company_title">
   <div class="manage_company_title_bg"><?php echo $menurow['typename']?></div>
     <div class="manage_company_main_text">
       <ul>
        <li><a href="<?php echo $menurow['useraddcon']; ?>?channelid=<?php echo $menurow['ID']?>">发布新<?php echo $menurow['typename']?></a></li>
        <li><a href="<?php echo $menurow['usermancon']; ?>?channelid=<?php echo $menurow['ID']?>">已发布<?php echo $menurow['typename']?></a></li>
       </ul>
    </div>
</div>
<?php
}}}
?>
</div>