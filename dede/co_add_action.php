<?php 
require_once(dirname(__FILE__)."/config.php");
CheckPurview('co_AddNote');

if($linkareas!=""&&$linkareae!="") $linkarea = $linkareas.'[var:区域]'.$linkareae;
else $linkarea = '';

if($sppages!="" && $sppagee!="") $sppage = $sppages.'[var:分页区域]'.$sppagee;
else $sppage = '';

$itemconfig = "
{!-- 节点基本信息 --}

{dede:item name=\\'$notename\\'
	imgurl=\\'$imgurl\\' imgdir=\\'$imgdir\\' language=\\'$language\\'
	isref=\\'$isref\\' refurl=\\'$refurl\\' exptime=\\'$exptime\\'
	typeid=\\'$exrule\\' matchtype=\\'$matchtype\\'}
{/dede:item}

{!-- 采集列表获取规则 --}

{dede:list source=\\'$source\\' sourcetype=\\'list\\' 
           varstart=\\'$varstart\\' varend=\\'$varend\\'}
  {dede:url value=\\'$sourceurl\\'}$sourceurls{/dede:url}	
  {dede:need}$need{/dede:need}
  {dede:cannot}$cannot{/dede:cannot}
  {dede:linkarea}$linkarea{/dede:linkarea}
{/dede:list}

{!-- 网页内容获取规则 --}

{dede:art}
{dede:sppage sptype=\\'$sptype\\'}$sppage{/dede:sppage}";	

for($i=1;$i<=50;$i++)
{
	if(!empty(${"field".$i}))
	{
		if(!isset($GLOBALS["value".$i])) $GLOBALS["value".$i] = "";
		else $GLOBALS["value".$i] = trim($GLOBALS["value".$i]);
		if(!isset($GLOBALS["match".$i])) $GLOBALS["match".$i] = "";
		
		if(!isset($GLOBALS["comment".$i])) $GLOBALS["comment".$i] = "";		
		if(!isset($GLOBALS["isunit".$i])) $GLOBALS["isunit".$i] = "";
		if(!isset($GLOBALS["isdown".$i])) $GLOBALS["isdown".$i] = "";
		if(!isset($GLOBALS["trim".$i])) $GLOBALS["trim".$i] = "";
		$trimstr = $GLOBALS["trim".$i];
		$GLOBALS["comment".$i] = str_replace("'","",$GLOBALS["comment".$i]);
		
		if($trimstr!=""&&!eregi("{dede:trim",$trimstr)){
			$trimstr = "    {dede:trim}$trimstr{/dede:trim}\r\n";
		}
		else{
			$trimstr = str_replace("{dede:trim","    {dede:trim",$trimstr); 
		}
	$matchstr = '';
	if( !empty($GLOBALS["matchs".$i]) && !empty($GLOBALS["matche".$i]) ){
		$matchstr = $GLOBALS["matchs".$i]."[var:内容]".$GLOBALS["matche".$i];
	}
	$itemconfig .= "
  
  {dede:note field=\\'".${"field".$i}."\\' value=\\'".$GLOBALS["value".$i]."\\' comment=\\'".$GLOBALS["comment".$i]."\\'
   isunit=\\'".$GLOBALS["isunit".$i]."\\' isdown=\\'".$GLOBALS["isdown".$i]."\\'}
    
    {dede:match}".$matchstr."{/dede:match}
    $trimstr
    {dede:function}".$GLOBALS["function".$i]."{/dede:function}
    
  {/dede:note}";
 }
}
$itemconfig .= "
{/dede:art}
";

$inQuery = "
INSERT INTO #@__conote(typeid,gathername,language,arcsource,lasttime,savetime,noteinfo) 
VALUES('$exrule', '$notename', '$language','$arcsource', '0','".time()."', '$itemconfig');
";
$dsql = new DedeSql(false);
if($dsql->ExecuteNoneQuery($inQuery))
{
	$dsql->Close();
	ShowMsg("成功增加一个节点!","co_main.php");
	exit();
}
else
{
	$gerr = $dsql->GetError();
	$dsql->Close();
	header("Content-Type: text/html; charset={$cfg_ver_lang}");
	echo "SQL语句：<xmp>$inQuery</xmp>";
	echo "<hr>错误提示：".$gerr."<hr>";
	ShowMsg("增加节点失败,请检查原因!","javascript:;");
	exit();
}

ClearAllLink();
?>