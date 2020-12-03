<?php
require_once(dirname(__FILE__)."/../include/common.inc.php");
require_once(DEDEINC."/channelunit.class.php");

if(isset($arcID)) $aid = $arcID;
$arcID = $aid = (isset($aid) && is_numeric($aid)) ? $aid : 0;
if($aid==0) die(" Request Error! ");

//读取文档信息
$arctitle = '';
$arcurl = '';
$topid = 0;
$arcRow = $dsql->GetOne("Select arc.title,arc.senddate,arc.arcrank,arc.ismake,arc.money,arc.typeid,tp.topid,tp.typedir,tp.namerule,
                 tp.moresite,tp.siteurl,tp.sitepath From `#@__archives` arc  left join `#@__arctype` tp on tp.id=arc.typeid where arc.id='$aid'");
if(is_array($arcRow))
{
	$arctitle = $arcRow['title'];
	$topid = $arcRow['topid'];
	$arcurl = @GetFileUrl($aid,$arcRow['typeid'],$arcRow['senddate'],$arctitle,$arcRow['ismake'],$arcRow['arcrank'],
	                    $arcRow['namerule'],$arcRow['typedir'],$arcRow['money'],$arcRow['filename'],$arcRow['moresite'],$arcRow['siteurl'],$arcRow['sitepath']);
}
else
{
	ShowMsg('无法浏览未知文档!','-1');
	exit();
}
if(empty($mx)){ $mx=$cfg_album_width; }
$pageGuide = "";
//获取上下幅图片链接
$row = $dsql->GetOne("Select imgurls From `#@__addonimages` where aid='{$aid}'");
$i = 0;
$nextSrc = '';
$preSrc = '';
$dtp = new DedeTagParse();
$dtp->LoadSource($row['imgurls']);
foreach($dtp->CTags as $ctag)
{
  if($ctag->GetName()=="img"){
    if($i==($npos-1)){ $preSrc = trim($ctag->GetInnerText()); }
    if($i==($npos+1)){ $nextSrc = trim($ctag->GetInnerText()); }
    $i++;
  }
}
unset($dtp);
if($cfg_multi_site=='Y'){
	if(!preg_match("/^http:/i",$preSrc) && !empty( $preSrc)) $preSrc = $cfg_basehost.$preSrc;
	if(!preg_match("/^http:/i",$nextSrc) && !empty($nextSrc)) $nextSrc = $cfg_basehost.$nextSrc;
}
if($preSrc!='')
{
	$pageGuide .= "<a href='showphoto.php?aid={$aid}&src=".urlencode($preSrc)."&npos=".($npos-1)."'>&lt;&lt;上一幅图片</a> ";
}
else
{
	$pageGuide .= "这是开始";
}
$nextlink = 'javascript:;';
if($nextSrc!='')
{
  $nextlink = "showphoto.php?aid={$aid}&src=".urlencode($nextSrc)."&npos=".($npos+1);
  if($pageGuide!="") $pageGuide .= " | ";
  $pageGuide .= "<a href='showphoto.php?aid={$aid}&src=".urlencode($nextSrc)."&npos=".($npos+1)."'>下一幅图片&gt;&gt;</a>";
}
else
{
	$pageGuide .= " | 没有了";
}
require_once(DEDETEMPLATE.'/plus/showphoto.htm');
exit();
?>