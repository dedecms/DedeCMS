<?php 
require_once(dirname(__FILE__)."/config.php");
if(empty($dopost)) $dopost = "";
if(empty($ID)) $ID="0";
$ID = ereg_replace("[^0-9]","",$ID);

//检查权限许可
CheckPurview('t_Edit,t_AccEdit');
//检查栏目操作许可
CheckCatalog($ID,"你无权更改本栏目！");

$dsql = new DedeSql(false);

//----------------------------------
//保存改动 Action Save
//-----------------------------------
if($dopost=="save")
{
	 $description = Html2Text($description);
   $keywords = Html2Text($keywords);
   
   if($cfg_cmspath!='') $typedir = ereg_replace("^".$cfg_cmspath,"{cmspath}",$typedir);
   //else if(!eregi("{cmspath}",$typedir) && $moresite==0) $typedir = "{cmspath}".$typedir;
   
   //子分类
   $sonlists = (empty($sonlists) ? '' : $sonlists);
   $smalltypes = "";
   if(is_array($sonlists) && isset($needson)){
   	 $n = count($sonlists);
   	 for($i=0;$i<$n;$i++){
   	 	 if($i==($n-1)) $smalltypes .= $sonlists[$i];
   	 	 else $smalltypes .= $sonlists[$i].",";
   	 }
   }
   
   if(empty($siterefer)) $siterefer=1;
   
   $upquery = "
     Update #@__arctype set
     sortrank='$sortrank',
     typename='$typename',
     typedir='$typedir',
     isdefault='$isdefault',
     defaultname='$defaultname',
     issend='$issend',
     channeltype='$channeltype',
     tempindex='$tempindex',
     templist='$templist',
     temparticle='$temparticle',
     tempone='$tempone',
     namerule='$namerule',
     namerule2='$namerule2',
     ispart='$ispart',
     corank='$corank',
     description='$description',
     keywords='$keywords',
     moresite='$moresite',
     siterefer='$siterefer',
     sitepath='$sitepath',
     siteurl='$siteurl',
     ishidden='$ishidden',
     smalltypes='$smalltypes'
   where ID='$ID'";
   
   if(!$dsql->ExecuteNoneQuery($upquery)){
   	 ShowMsg("保存当前栏目更改时失败，请检查你的输入资料是否存在问题！","-1");
   	 exit();
   }
   
   //更改本栏目文档的权限
   
   if($corank != $corank_old){
      $dsql->ExecuteNoneQuery("Update #@__archives set arcrank='$corank' where typeid='$ID' ");
   }
   
   //如果选择子栏目可投稿，更新顶级栏目及频道模型为可投稿
   if($issend==1){
   	 if($topID>0) $dsql->ExecuteNoneQuery("Update `#@__arctype` set issend='1' where ID='$topID'; ");
   	 $dsql->ExecuteNoneQuery("Update `#@__channeltype` set issend='1' where ID='$channeltype'; ");
   }
   
   //更新树形菜单
   $rndtime = time();
   $rflwft = "
   <script language='javascript'>
   <!--
   if(window.navigator.userAgent.indexOf('MSIE')>=1){
     if(top.document.frames.menu.location.href.indexOf('catalog_menu.php')>=1)
     { top.document.frames.menu.location = 'catalog_menu.php?$rndtime'; }
   }else{
  	 if(top.document.getElementById('menu').src.indexOf('catalog_menu.php')>=1)
     { top.document.getElementById('menu').src = 'catalog_menu.php?$rndtime'; }
   }
   -->
   </script>
   ";

//"-------------------------------
   
   //更改子栏目属性
   if(!empty($upnext))
   {
   	 require_once(dirname(__FILE__)."/../include/inc_typelink.php");
   	 $tl = new TypeLink($ID);
   	 $slinks = $tl->GetSunID($ID,'###',0);
   	 $slinks = str_replace("###.typeid","ID",$slinks);
   	 $upquery = "
       Update #@__arctype set
       issend='$issend',
       defaultname='$defaultname',
       channeltype='$channeltype',
       tempindex='$tempindex',
       templist='$templist',
       temparticle='$temparticle',
       namerule='$namerule',
       namerule2='$namerule2',
       moresite='$moresite',
       siterefer='$siterefer',
       sitepath='$sitepath',
       siteurl='$siteurl',
       ishidden='$ishidden',
       smalltypes='$smalltypes'
     where 1=1 And $slinks";
   
     if(!$dsql->ExecuteNoneQuery($upquery)){
       echo $rflwft;
   	   ShowMsg("更改当前栏目成功，但更改下级栏目属性时失败！","-1");
   	   exit();
     }
   }
   //更新缓存
   UpDateCatCache($dsql);
   $dsql->Close();
   echo $rflwft;
   ShowMsg("成功更改一个分类！","catalog_main.php");
   exit();
}//End Save Action


$dsql->SetQuery("Select #@__arctype.*,#@__channeltype.typename as ctypename From #@__arctype left join #@__channeltype on #@__channeltype.ID=#@__arctype.channeltype where #@__arctype.ID=$ID");
$myrow = $dsql->GetOne();
$topID = $myrow['topID'];
if($topID>0)
{
	$toprow = $dsql->GetOne("Select moresite,siterefer,sitepath,siteurl From #@__arctype where ID=$topID");
	foreach($toprow as $k=>$v){
	  if(!ereg("[0-9]",$k)) $myrow[$k] = $v;
	}
}
//读取频道模型信息
$channelid = $myrow['channeltype'];
$row = $dsql->GetOne("select * from #@__channeltype where ID='$channelid'");
$nid = $row['nid'];
//读取所有模型资料
$dsql->SetQuery("select * from #@__channeltype where ID<>-1 And isshow=1 order by ID");
$dsql->Execute();
while($row=$dsql->GetObject())
{
  $channelArray[$row->ID]['typename'] = $row->typename;
  $channelArray[$row->ID]['nid'] = $row->nid;
}
//父栏目是否为二级站点
if(!empty($myrow['moresite'])){
	 $moresite = $myrow['moresite'];
}else{
	 $moresite = 0;
}

if($myrow['topID']==0){
	PutCookie('lastCid',$ID,3600*24,"/");
}

require_once(dirname(__FILE__)."/templets/catalog_edit.htm");

ClearAllLink();

?>