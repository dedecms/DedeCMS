<?php
if(!defined('DEDEINC')) exit('dedecms');

function multipage($allItemTotal, $currPageNum, $pageSize, $tagid=''){
if ($allItemTotal == 0) return "";

//计算总页数
$pagesNum = ceil($allItemTotal/$pageSize);

//第一页显示
$firstPage = ($currPageNum <= 1) ? $currPageNum ."</b>&lt;&lt;" : "<a href='javascript:multi(1,\"{$tagid}\")' title='第1页'>1&lt;&lt;</a>";

//最后一页显示
$lastPage = ($currPageNum >= $pagesNum)? "&gt;&gt;". $currPageNum : "<a href='javascript:multi(". $pagesNum . ",\"{$tagid}\")' title='第". $pagesNum ."页'>&gt;&gt;". $pagesNum ."</a>";

//上一页显示
$prePage  = ($currPageNum <= 1) ? "上页" : "<a href='javascript:multi(". ($currPageNum-1) . ",\"{$tagid}\")'  accesskey='p'  title='上一页'>[上一页]</a>";

//下一页显示
$nextPage = ($currPageNum >= $pagesNum) ? "下页" : "<a href='javascript:multi(". ($currPageNum+1) .",\"{$tagid}\")' title='下一页'>[下一页]</a>";

//按页显示
$listNums = "";
for ($i=($currPageNum-4); $i<($currPageNum+9); $i++) {
	if ($i < 1 || $i > $pagesNum) continue;
	if ($i == $currPageNum) $listNums.= "<a href='javascript:void(0)' class='thislink'>".$i."</a>";
	else $listNums.= " <a href='javascript:multi(". $i .",\"{$tagid}\")' title='". $i ."'>". $i ."</a> ";
}

$returnUrl = $listNums;

return $returnUrl;
}
?>