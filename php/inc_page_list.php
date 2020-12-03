<?
/*----分页列表输出函数------------------------------------
 $get_var 页面链接
 $total_record 总记录数
 $page_size 每页记录数  (可选,默认为20)
 $list_len 列表长度     (可选，默认为5,实际长度为 $list_len*2+1)
 例：get_page_list("inc_page_list.php",323,7,5);
---------------------------------------------------------*/
function get_page_list($get_var,$total_record,$page_size=20,$list_len=5)
{
    global $page;
    if($total_record!=0){
	if(!ereg("\?",$get_var)) $get_var.="?tag=0";
	if($page=="") $page=1;
	if($total_record%$page_size!=0)
	$total_page=ceil($total_record/$page_size);
	else
	$total_page=$total_record/$page_size;
	$prepage = $page-1;
	$nextpage = $page+1;
	echo "共".$total_record."条记录 ".$page."/".$total_page."页 ";
	if($prepage>0){
		echo "<a href='".$get_var."&total_record=".$total_record."'>首页</a>\r\n";
		echo "<a href='".$get_var."&total_record=".$total_record."&page=".$prepage."'>上一页</a>\r\n";
	}
	$total_list = $list_len * 2 + 1;
        if($page>=$total_list) 
        {
        	$i=$page-$list_len;
        	$total_list=$page+$list_len;
        	if($total_list>$total_page) $total_list=$total_page;
        }	
        else
        { 
        	$i=1;
        	if($total_list>$total_page) $total_list=$total_page;
        }
        for($i;$i<=$total_list;$i++)
        {
        	if($i==$page) echo "$i ";
        	else echo "<a href='".$get_var."&total_record=".$total_record."&page=".$i."'>[".$i."]</a>\r\n";
        }
        if($nextpage<=$total_page){
		echo "<a href='".$get_var."&page=".$nextpage."&total_record=".$total_record."'>下一页</a>\r\n";
		echo "<a href='".$get_var."&total_record=".$total_record."&page=".$total_page."'>未页</a>\r\n";
	}
   }
   else
   {
   	echo "没任何记录！";
   }
	
}
/*-----------------------计算总页数--------------------------*/
function get_total_page($total_record,$page_size)
{
	return ceil($total_record/$page_size);
}
/*---------------get_limit，返回mysql分页查询时的 limit 条件-----*/
function get_limit($page_size)
{
	global $page;
	if($page=="") $page=1;
	$limit_start = ($page-1)*$page_size;
	return " limit ".$limit_start.",".$page_size." ";
}
?>