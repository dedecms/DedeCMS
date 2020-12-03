<?
//原来的这个文件的全部功能已经转移到 inc_typeunit2.php
//目前保留的仅是获取下级目录ID的函数
//--------------------------------
require_once(dirname(__FILE__)."/../include/config_base.php");
$idArrary = "";
//------------------------------------------------------
//-----返回与某个目相关的下级目录的类目ID列表(删除类目或文章时调用)
//------------------------------------------------------
function TypeGetSunTypes($ID,$dsql,$channel=0)
{
		if($ID!=0) $GLOBALS['idArray'][$ID] = $ID;
		$fid = $ID;
	  if($channel!=0) $csql = " And channeltype=$channel ";
	  else $csql = "";
		$dsql->SetQuery("Select ID From #@__arctype where reID=$ID $csql");
		$dsql->Execute("gs".$fid);
		while($row=$dsql->GetObject("gs".$fid)){
			TypeGetSunTypes($row->ID,$dsql,$channel);
		}
		return $GLOBALS['idArray'];
}
//----------------------------------------------------------------------------
//获得某ID的下级ID(包括本身)的SQL语句“($tb.typeid=id1 or $tb.typeid=id2...)”
//----------------------------------------------------------------------------
function TypeGetSunID($ID,$dsql,$tb="#@__archives",$channel=0)
{
		$GLOBALS['idArray'] = "";
		TypeGetSunTypes($ID,$dsql,$channel);
		$rquery = "";
		foreach($GLOBALS['idArray'] as $k=>$v){
			if($tb!="")
			{
			  if($rquery!="") $rquery .= " Or ".$tb.".typeid='$k' ";
			  else      $rquery .= "    ".$tb.".typeid='$k' ";
		  }
		  else
		  {
		  	if($rquery!="") $rquery .= " Or typeid='$k' ";
			  else      $rquery .= "    typeid='$k' ";
		  }
		}
		return " (".$rquery.") ";
}
?>