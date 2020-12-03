<?php
if(!defined('DEDEINC')) exit('Request Error!');
//引入分页函数
require_once(DEDEINC.'/page.func.php');

function lib_arcpagelist(&$ctag, &$refObj)
{
	global $dsql;
	$attlist = "tagid|,style|1";
	FillAttsDefault($ctag->CAttribute->Items,$attlist);
	extract($ctag->CAttribute->Items, EXTR_SKIP);
	
	$row = $dsql->GetOne("SELECT * FROM #@__arcmulti WHERE tagid='$tagid'");
	if(is_array($row))
	{
	  $ids = explode(',', $row['arcids']);
	
	  $totalnum = count($ids);
	  $pagestr = '<div id="page_'.$tagid.'">';
	  if($row['pagesize'] < $totalnum)
	  {
	    $pagestr .= multipage($totalnum, 1, $row['pagesize'], $tagid);
	  } else {
	  	$pagestr .= '共1页';
	  }
	  $pagestr .= '</div>';
	  return $pagestr;
	} else {
	  $pagestr = '<div id="page_'.$tagid.'">';
	  $pagestr .= '没有检索到对应分页';
	  $pagestr .= '</div>';
		return $pagestr;
	}
}

?>