<?
require("config.php");
class GuestBook
{
	var $nowPage=1;
	var $totalResult=0;
	var $pageSize=10;
	var $con;
	function GuestBook($pages,$npage,$totalrs)
	{
		if($npage==0) $npage=1;
		$this->nowPage = $npage;
		$this->totalResult = $totalrs;
		$this->pageSize = $pages;
		$this->con = connectMySql();
	}
	function GetTotal()
	{
		if($this->totalResult==0)
		{
			$rs = mysql_query("select count(ID) as dd from dede_guestbook where ischeck=1",$this->con);
			$row = mysql_fetch_array($rs);
			$this->totalResult = $row["dd"];
		}
		return $this->totalResult;
	}
	//返回总页数
	function getTotalPage()
	{
		return ceil($this->GetTotal()/$this->pageSize);
	}
	//返回mysql分页查询时的 limit 条件
	function getLimit()
	{
		if($this->nowPage==""||$this->nowPage==0) $this->nowPage=1;
		$limit_start = ($this->nowPage-1)*$this->pageSize;
		return " limit ".$limit_start.",".$this->pageSize." ";
	}	
	//分页列表输出函数------------------------------------
 	//$get_var 页面链接
 	//$total_record 总记录数
 	//$page_size 每页记录数  (可选,默认为20)
 	//$list_len 列表长度     (可选，默认为5,实际长度为 $list_len*2+1)
 	//例：get_page_list("inc_page_list.php",323,7,5);
	//////////////////////////////////////////////////////
	function getPageList($get_var,$list_len=5)
	{
    	$totalrecord = $this->GetTotal();
    	if($totalrecord!=0)
    	{
			if(!ereg("\?",$get_var)) $get_var.="?tag=0";
			if($this->nowPage==""||$this->nowPage==0) $this->nowPage=1;
			$totalpage=$this->getTotalPage();
			$prepage = $this->nowPage-1;
			$nextpage = $this->nowPage+1;
			echo "共".$this->GetTotal()."条记录 ".$this->nowPage."/".$totalpage."页 ";
			if($prepage>0)
			{
				echo "<a href='".$get_var."&totalrecord=".$totalrecord."&page=1'>首页</a>\r\n";
				echo "<a href='".$get_var."&totalrecord=".$totalrecord."&page=".$prepage."'>上一页</a>\r\n";
			}
			$total_list = $list_len * 2 + 1;
        	if($this->nowPage >= $total_list) 
        	{
        		$i=$this->nowPage-$list_len;
        		$total_list=$this->nowPage+$list_len;
        		if($total_list>$totalpage) $total_list=$totalpage;
        	}	
        	else
        	{ 
        		$i=1;
        		if($total_list>$totalpage) $total_list=$totalpage;
       	 	}
        	for($i;$i<=$total_list;$i++)
        	{
        		if($i==$this->nowPage) echo "$i ";
        		else echo "<a href='".$get_var."&totalrecord=".$totalrecord."&page=".$i."'>[".$i."]</a>\r\n";
        	}
        	if($nextpage<=$totalpage)
        	{
				echo "<a href='".$get_var."&page=".$nextpage."&totalrecord=".$totalrecord."'>下一页</a>\r\n";
				echo "<a href='".$get_var."&totalrecord=".$totalrecord."&page=".$totalpage."'>未页</a>\r\n";
			}
   		}
   		else
   		{
   			echo "没任何记录！";
   		}
	}
	//内容输出函数
	function printResult()
	{
		if($this->GetTotal()>0)
		{
			$rs = mysql_query("select * from dede_guestbook where ischeck=1 order by ID desc".$this->getLimit(),$this->con);
			while($row=mysql_fetch_object($rs))
			{
				$msg = "
				<table width='760' border='0' cellpadding='3' cellspacing='1' bgcolor='#E6D85A'>
  <tr>
    <td width='160' rowspan='3' valign='top' bgcolor='#FFFFFF'>
    <table border='0' cellPadding='4' cellSpacing='0' width='100%'>
      <tr>
        <td align=center width='17%'><img src='images/".$row->face.".gif' border=0></td>
      </tr>
    </table>
      <P> 
     &nbsp;姓名：".$row->uname."<br>
	 &nbsp;来自：".$row->ip."<br>
	 &nbsp;QQ：".$row->qq."<br>
	</P></td>
    <td width='600' bgcolor='#FFFFFF'><img height='16' src='images/time.gif' width='16'> 发表时间: ".$row->dtime." </td>
  </tr>
  <tr>
    <td height='100' bgcolor='#FFFFFF'>
    ".$row->msg."
    </td>
  </tr>
  <tr>
    <td bgcolor='#FFFFFF'>
    <a href='mailto:".$row->email."'><img src='images/mail.gif' border=0 width='16' height='16'>[邮件]</a> 
    <a href='http://".$row->homepage."' target='_blank'><img src='images/home.gif' border=0 width='16' height='16'>[主页]</a>  
    <a href='edit.php?ID=".$row->ID."'><img src='images/quote.gif' border=0 height=16 width=16>[回复/编辑]</a>  
    <a href='edit.php?ID=".$row->ID."&job=del'><img src='images/del.gif' border=0 height=16 width=16>[删除]</a> 
    </td>
  </tr>
</table>
<table width='760'><td height='2'></td></table>
				";
				echo $msg;
			}
		}
	}
}
?>