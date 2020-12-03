<?php
if(!isset($cfg_main_dftable)) exit();

$svali = GetCkVdValue();
if(strtolower($vdcode)!=$svali || $svali==""){
    ShowMsg("验证码错误！","-1");
    exit();
}

require_once(dirname(__FILE__)."/inc/inc_archives_functions.php");
require_once(dirname(__FILE__)."/inc/inc_catalog_options.php");

$channelid = intval($channelid);
$typeid = intval($typeid);
$dede_addonfields = (empty($dede_addonfields) ? '' : $dede_addonfields);
$dede_fieldshash = (empty($dede_fieldshash) ? '' : $dede_fieldshash);

if(!empty($dede_addonfields))
{
	 $_dede_addonfields = md5($dede_addonfields.$cfg_cookie_encode);
	 if($_dede_addonfields!=$dede_fieldshash){
		  ShowMsg("附加数据校验出错，不允许发布！","-1");
	    exit();
	}
}

if($typeid==0){
	 ShowMsg("请指定信息隶属的栏目！","-1");
	 exit();
}

$dsql = new DedeSql(false);
$_msg = CheckChannel($typeid,$channelid);
if($_msg!=''){
	$dsql->Close();
	ShowMsg("系统出错，原因是：{$_msg}","-1");
	exit();
}

$cInfos = $dsql->GetOne("Select * From #@__channeltype  where ID='$channelid'; ");	
if($cInfos['issend']!=1){
	$dsql->Close();
	ShowMsg("你指定的频道参数的错误！","-1");
	exit();
}

if($cInfos['sendrank'] > $cfg_ml->M_Type){
	$row = $dsql->GetOne("Select membername From #@__arcrank where rank='".$cInfos['sendrank']."' ");
	$dsql->Close();
	ShowMsg("对不起，需要 [".$row['membername']."] 才能在这个频道发布文档！","-1","0",5000);
	exit();
}

if(isset($cfg_isalbum)) CheckUserSpace($cfg_ml->M_ID);

//获取附加表，处理附加数据
$maintable = ($cInfos['maintable']=='' ? $cfg_main_dftable : $cInfos['maintable']);
$addtable = ($cInfos['addtable']=='' ? $cfg_add_dftable : $cInfos['addtable']);
$arcID = GetIndexKey($dsql,$typeid,$channelid);

//分析处理附加表数据
$description = (empty($description) ? '' : $description);
$inadd_f = '';
$inadd_v = '';
if(!empty($dede_addonfields))
{
  $addonfields = explode(";",$dede_addonfields);
  $inadd_f = "";
  $inadd_v = "";
  if(is_array($addonfields))
  {
    foreach($addonfields as $v)
    {
	     if($v=="") continue;
	     $vs = explode(",",$v);
	     //HTML文本特殊处理
	     if($vs[1]=="htmltext"||$vs[1]=="textdata")
	     {
		     ${$vs[0]} = filterscript(stripslashes(${$vs[0]}));
             //自动摘要
             if(empty($description)){
    	        $description = cn_substr(html2text(${$vs[0]}),$cfg_auot_description);
	            $description = trim(preg_replace("/#p#|#e#/","",$description));
	            $description = addslashes($description);
            }
           ${$vs[0]} = addslashes(${$vs[0]});
           ${$vs[0]} = GetFieldValue(${$vs[0]},$vs[1],$arcID,'add','','member');
	     }else{
		     ${$vs[0]} = GetFieldValueA(${$vs[0]},$vs[1],$arcID);
	     }
	     $inadd_f .= ",".$vs[0];
	     $inadd_v .= ",'".${$vs[0]}."'";
    }
  }
}

?>