<?
function listpage($submitfile,$total,$page,$pagesize,$getvar){
       $pre=$page-1;
       $nextpage=$page+1;
       $i=0;
       $end=0;
       $totalpage = ceil($total/$pagesize);
       if($totalpage==0) $page=0;
       echo "共($page/$totalpage)页&nbsp;\n";
       if($totalpage>1) echo "<a href=".$submitfile."?total=$total&page=1".$getvar.">首页</a>\n";	
       if($total!=0){
		if($pre!=0) echo "<a href=".$submitfile."?total=$total&page=$pre".$getvar.">↑上一页</a>&nbsp;\n";
		else echo "↑上一页&nbsp;";
	}
        if(($page-3)>0){
        	if($total>=($page+3))
        	{
        	          $i=$page-3;
        	          $end=$page+3;
        	}
        	else 
        	{
        		$i=$total-6;
        	        $end=$total;
        	}        
        }
        else {$i=1;$end=10;}
	for(;$i<$end;$i++){	
             if($i>$totalpage) break;	
	     if($i!=$page) echo "<a href=".$submitfile."?total=$total&page=$i".$getvar.">[".$i."]</a>\n";
	     else echo $i."&nbsp;";
	}
	if($total!=0){
	     if($nextpage <= $totalpage) echo "<a href=".$submitfile."?total=$total&page=$nextpage".$getvar.">下一页↓</a>\n";
	     else echo "下一页↓";
	} 
	if($totalpage>1) echo "&nbsp;<a href=".$submitfile."?total=$total&page=$totalpage".$getvar.">末页</a>\n";
}
?>	  