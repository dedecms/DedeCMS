<?php
require_once(dirname(__FILE__)."/config.php");
if(empty($ID)) $ID = 0;
if(empty($listtype)) $listtype="";
if(empty($dopost)) $dopost = "";
if(empty($channelid)) $channelid = 1;
$ID = intval($ID);
if($ID==0){ CheckPurview('t_New'); }
else{
	CheckPurview('t_AccNew');
	CheckCatalog($ID,"你无权在本栏目下创建子类！");
}

$dsql = new DedeSql(false);


//保存栏目
/*------------------------
function __SaveCatalog()
--------------------------*/
if($dopost=="save")
{
   if(empty($reID)) $reID = 0;
   if(empty($upinyin)) $upinyin = 0;
   $description = Html2Text($description);
   $keywords = Html2Text($keywords);

   
   $tmpdir = $typedir;
   if($ispart==3 && $typedir==''){
     ShowMsg("你设置的栏目属性是跳转网址，请指定要跳转的网址！","-1");
     exit();
   }

 if($ispart!=3) //非跳转网址处理栏目目录
 {
   //栏目的参照目录
   if($reID==0 && $moresite==1) $nextdir = '/';
   else{
     if($referpath=='cmspath') $nextdir = '{cmspath}';
     else if($referpath=='basepath') $nextdir = '';
     else $nextdir = $nextdir;
   }
   //用拼音命名
   if( $upinyin==1 || ( $typedir=='' && $sitepath=='' ) || ( $typedir=='' && $moresite==1 && $reID>0 ) )
   {
     	 $typedir = GetPinyin($typename);
   }

   $typedir = $nextdir."/".$typedir;

   $typedir = ereg_replace("/{1,}","/",$typedir);

   if($referpath=='basepath' && $siteurl!='') $typedir = '';

   //检测二级网址
   if($siteurl!="")
   {
      $siteurl = ereg_replace("/$","",$siteurl);
      if(!eregi("http://",$siteurl)){
      	$dsql->Close();
   	    ShowMsg("你绑定的二级域名无效，请用(http://域名)的形式！","-1");
   	    exit();
      }
      if(eregi($cfg_basehost,$siteurl)){
      	$dsql->Close();
   	    ShowMsg("你绑定的二级域名与当前站点是同一个域名，不需要绑定！","-1");
   	    exit();
      }
   }

   //创建目录
   $true_typedir = str_replace("{cmspath}",$cfg_cmspath,$typedir);
   $true_typedir = ereg_replace("/{1,}","/",$true_typedir);
   if(!CreateDir($true_typedir,$siterefer,$sitepath))
   {
   	  $dsql->Close();
   	  ShowMsg("创建目录 {$true_typedir} 失败，请检查你的路径是否存在问题！","-1");
   	  exit();
   }
 }//非跳转网址处理栏目目录

   if($channeltype == '-2') $isdefault = '-1';

	 //子分类
   $sonlists = (empty($sonlists) ? '' : $sonlists);
   $smalltypes = '';
   if(is_array($sonlists) && isset($needson)){
   	 $n = count($sonlists);
   	 for($i=0;$i<$n;$i++){
   	 	 if($i==($n-1)) $smalltypes .= $sonlists[$i];
   	 	 else $smalltypes .= $sonlists[$i].",";
   	 }
   }


   $in_query = "insert into #@__arctype(
    reID,sortrank,typename,typedir,isdefault,defaultname,issend,channeltype,
    tempindex,templist,temparticle,tempone,modname,namerule,namerule2,
    ispart,corank,description,keywords,moresite,siterefer,sitepath,siteurl,ishidden,smalltypes)Values(
    '$reID','$sortrank','$typename','$typedir','$isdefault','$defaultname','$issend','$channeltype',
    '$tempindex','$templist','$temparticle','$tempone','default','$namerule','$namerule2',
    '$ispart','$corank','$description','$keywords','$moresite','$siterefer','$sitepath','$siteurl','$ishidden','$smalltypes')";


   if(!$dsql->ExecuteNoneQuery($in_query))
   {
   	 $dsql->Close();
   	 ShowMsg("保存目录数据时失败，请检查你的输入资料是否存在问题！","-1");
   	 exit();
   }
   //更新缓存
   UpDateCatCache($dsql);
   
   //如果选择子栏目可投稿，频道模型为可投稿
   $topID = (empty($topID) ? '0' : $topID);
   if($issend==1){
   	 if($topID>0) $dsql->ExecuteNoneQuery("Update `#@__arctype` set issend='1' where ID='$topID'; ");
   	 $dsql->ExecuteNoneQuery("Update `#@__channeltype` set issend='1' where ID='$channeltype'; ");
   }
   
   $dsql->Close();
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
   
//"--------------------

   echo $rflwft;
   ShowMsg("成功创建一个分类！","catalog_main.php");
   exit();

}
//End dopost==save
//结束保存栏目事件

//--------------------------
//读取父类参数
//----------------------------
$myrow['moresite'] = $myrow['siterefer'] = $myrow['sitepath'] = $myrow['siteurl'] = '';
$issend = 1;
$corank = 0;

if($ID>0)
{
  $myrow = $dsql->GetOne("Select #@__arctype.*,#@__channeltype.typename as ctypename From #@__arctype left join #@__channeltype on #@__channeltype.ID=#@__arctype.channeltype where #@__arctype.ID=$ID");
	$issennd = $myrow['issend'];
	$corank = $myrow['corank'];
	$topID = $myrow['topID'];
	$issend = $myrow['issend'];
	$corank = $myrow['corank'];
	$typedir = $myrow['typedir'];
}
//读取频道模型信息
if(isset($myrow['channeltype'])) $channelid = $myrow['channeltype'];
else $channelid = 1;
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

require_once(dirname(__FILE__)."/templets/catalog_add.htm");

ClearAllLink();

?>