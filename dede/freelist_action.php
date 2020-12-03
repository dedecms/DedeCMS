<?php
require_once(dirname(__FILE__)."/config.php");
CheckPurview('c_FreeList');
if(!isset($types)) $types = '';
if(!isset($nodefault)) $nodefault = '0';
 /*-------------
 function __AddNew()
 --------------*/
if($dopost=='addnew'){
   $atts = " pagesize='$pagesize' col='$col' titlelen='$titlelen' orderby='$orderby' orderway='$order' ";
   $ntype = '';
   $edtime = time();
   if(empty($channel)) {showmsg('频道类型不能为空','-1');exit();}
   if(is_array($types)) foreach($types as $v) $ntype .= $v.' ';
   if($ntype!='') $atts .= " type='".trim($ntype)."' ";
   if(!empty($typeid)) $atts .= " typeid='$typeid' ";
   if(!empty($channel)) $atts .= " channel='$channel' ";
   if(!empty($subday)) $atts .= " subday='$subday' ";
   if(!empty($titlelen)) $atts .= " keyword='$keyword' ";
   if(!empty($att)) $atts .= " att='$att' ";
   $innertext = trim($innertext);
   if(!empty($innertext)) $innertext = stripslashes($innertext);
   $listTag = "{dede:list $atts}$innertext{/dede:list}";
   $listTag = addslashes($listTag);
   $inquery = "
     INSERT INTO `#@__freelist`(`title` , `namerule`  , `listdir` , `defaultpage` , `nodefault` , `templet` , `edtime` , `click` , `listtag` , `keyword` , `description`)
     VALUES ('$title','$namerule','$listdir','$defaultpage','$nodefault','$templet','$edtime','0','$listTag','$keyword','$description');
   ";
   $dsql = new DedeSql(false);
   $dsql->ExecuteNoneQuery($inquery);
   $dsql->Close();
   ShowMsg("成功增加一个自由列表!","freelist_main.php");
   exit();
}
/*-------------
 function __Edit()
--------------*/
if($dopost=='edit'){
   $atts = " pagesize='$pagesize' col='$col' titlelen='$titlelen' orderby='$orderby' orderway='$order' \r\n";
   $ntype = '';
   $edtime = time();
   if(is_array($types)) foreach($types as $v) $ntype .= $v.' ';
   if($ntype!='') $atts .= " type='".trim($ntype)."' ";
   if(!empty($typeid)) $atts .= " typeid='$typeid' ";
   if(!empty($channel)) $atts .= " channel='$channel' ";
   if(!empty($subday)) $atts .= " subday='$subday' ";
   if(!empty($titlelen)) $atts .= " keyword='$keyword' ";
   if(!empty($att)) $atts .= " att='$att' ";
   $innertext = trim($innertext);
   if(!empty($innertext)) $innertext = stripslashes($innertext);
   $listTag = "{dede:list $atts}$innertext{/dede:list}";
   $listTag = addslashes($listTag);

   $inquery = "
     Update `#@__freelist` set
     title='$title', namerule='$namerule',
     listdir='$listdir', defaultpage='$defaultpage',
     nodefault='$nodefault', templet='$templet',
     edtime='$edtime', listtag='$listTag', keyword='$keyword',
     description='$description' where aid='$aid';
   ";

   $dsql = new DedeSql(false);
   $dsql->ExecuteNoneQuery($inquery);
   $dsql->Close();

   ShowMsg("成功更改一个自由列表!","freelist_main.php");
   exit();
}
ClearAllLink();
?>