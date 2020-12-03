<?php
if($cfg_ml->M_utype == 0){
	 require_once(dirname(__FILE__)."/menu.php");
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

<!-- //企业空间 -->
<div id="manage_company_left">
        	<div class="manage_company_title">
                <div class="manage_company_title_bg">基本资料</div>
                <div class="manage_company_main_text">
                    <ul>
                    <li><a href="com_edit_pwd.php">修改密码</a></li>
                	<li><a href="mycominfo.php">企业资料</a></li>
                	<li><a href="mycominfo.php?action=culture">企业文化</a></li>
                    </ul>
                </div>
            </div>

            <div class="manage_company_title">
                <div class="manage_company_title_bg">工具箱</div>
                <div class="manage_company_main_text">
                    <ul>
                    <li><a href="money2score.php">金币、积分兑换</a></li>
                    <li><a href="comstow.php">我的收藏夹</a></li>
                	<li><a href="comguestbook.php">我的留言簿</a></li>
                	<li><a href="comupload.php">附件管理</a></li>
                    <li><a href="comlink.php">友情链接</a></li>
                	<li><a href="com_archives_type.php">管理我的分类</a></li>
                	<li><a href="myorder.php">在线意向定单</a></li>
                    </ul>
                </div>
            </div>

            <div class="manage_company_title">
                <div class="manage_company_title_bg">信息发布</div>
                <div class="manage_company_main_text">
                    <ul>
                    <li><a href="do.php?action=add&channelid=-2">供求信息发布</a></li>
                	<li><a href="do.php?action=list&channelid=-2">供求信息管理</a></li>
                	<li><a href="addjob.php">发布招聘信息</a></li>
                    <li><a href="joblist.php">招聘信息管理</a></li>
                    </ul>
                </div>
            </div>

            <div class="manage_company_title">
                <div class="manage_company_title_bg">产品管理</div>
                <div class="manage_company_main_text">
                    <ul>
                    <li><a href="archives_add.php?channelid=5">发布新产品</a></li>
                	<li><a href="content_list.php?channelid=5">已发布产品管理</a></li>
                    </ul>
                </div>
            </div>

            <!-- add -->
	
	
            <div class="manage_company_title">
                <div class="manage_company_title_bg">文章管理</div>
                <div class="manage_company_main_text">
                    <ul>
                    <li><a href="article_add.php">发表新文章</a></li>
                	<li><a href="content_list.php?channelid=1">已发表的文章</a></li>
                    </ul>
                </div>
            </div>
<?php
if($cfg_mb_sendall=='Y'){
if(!isset($dsql) || !is_object($dsql)){
	$dsql = new DedeSql(false);
}
$dsql->SetQuery("Select ID,typename,useraddcon,usermancon From #@__channeltype where issend=1 And issystem=0 And sendmember>0 ");
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
}}
?>
</div>
<?php
}
?>