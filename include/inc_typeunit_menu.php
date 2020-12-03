<?php 
//class TypeUnit
//这个类主要是封装频道管理时的一些复杂操作 
//--------------------------------
require_once(dirname(__FILE__)."/config_base.php");
require_once(dirname(__FILE__)."/../data/cache/inc_catalog_base.php");
class TypeUnit
{
	var $dsql;
	var $artDir;
	var $baseDir;
	var $idCounter;
	var $idArrary;
	var $shortName;
	var $aChannels;
	var $isAdminAll;
	//-------------
	//php5构造函数
	//-------------
	function __construct($catlogs='')
 	{
		global $_Cs;
		$this->idCounter = 0;
		$this->artDir = $GLOBALS['cfg_cmspath'].$GLOBALS['cfg_arcdir'];
		$this->baseDir = $GLOBALS['cfg_basedir'];
		$this->shortName = $GLOBALS['art_shortname'];
		$this->idArrary = "";
		$this->dsql = new DedeSql(false);
		$this->aChannels = Array();
		$this->isAdminAll = false;
		if(!empty($catlogs) && $catlogs!='-1')
		{
			$this->aChannels = explode(',',$catlogs);
			foreach($this->aChannels as $cid)
			{
				if($_Cs[$cid][0]==0)
				{
					 $this->dsql->SetQuery("Select ID,ispart From `#@__arctype` where reID=$cid");
					 $this->dsql->Execute();
					 while($row = $this->dsql->GetObject()){
						 if($row->ispart!=2) $this->aChannels[] = $row->ID;
					 }
				}
			}
		}else{
			$this->isAdminAll = true;
		}
  }
	function TypeUnit($catlogs='')
	{
		$this->__construct($catlogs);
	}
	//------------------
	//清理类
	//------------------
	function Close()
	{
		if($this->dsql){
			@$this->dsql->Close();
			@$this->dsql=0;
		}
		$this->idArrary = "";
		$this->idCounter = 0;
	}
	//
	//----读出所有分类,在类目管理页(list_type)中使用----------
	//
	function ListAllType($channel=0,$nowdir=0)
	{
		
		$this->dsql->SetQuery("Select ID,typedir,typename,ispart,channeltype From #@__arctype where reID=0 And ispart<>3 order by sortrank");
		$this->dsql->Execute(0);
		
		$lastID = GetCookie('lastCidMenu');
		
		while($row=$this->dsql->GetObject(0))
		{	 
			$typeDir = $row->typedir;
			$typeName = $row->typename;
			$ispart = $row->ispart;
			$ID = $row->ID;
			$channeltype = $row->channeltype;
			
			if($ispart==2){
				continue;
			}
			
			//有权限栏目
			if($this->isAdminAll===true || in_array($ID,$this->aChannels))
			{
			   //互动栏目
			   if($channeltype<-1) $smenu = " oncontextmenu=\"CommonMenuWd(this,$ID,'".urlencode($typeName)."')\"";
			   //普通列表
			   else if($ispart==0) $smenu = " oncontextmenu=\"CommonMenu(this,$ID,'".urlencode($typeName)."')\"";
			   //带封面的频道
			   else if($ispart==1) $smenu = " oncontextmenu=\"CommonMenuPart(this,$ID,'".urlencode($typeName)."')\"";
			   //独立页面
			   else if($ispart==2) $smenu = " oncontextmenu=\"SingleMenu(this,$ID,'".urlencode($typeName)."')\"";
			   //跳转
			   else if($ispart==3) $smenu = " ";
			   
			   echo "<dl class='topcc'>\r\n";
			   echo "  <dd class='dlf'><img style='cursor:hand' onClick=\"LoadSuns('suns{$ID}',{$ID});\" src='img/tree_explode.gif' width='11' height='11'></dd>\r\n";
			   echo "  <dd class='dlr'><a href='catalog_do.php?cid=".$ID."&dopost=listArchives'{$smenu}>".$typeName."</a></dd>\r\n";
			   echo "</dl>\r\n";
			   echo "<div id='suns".$ID."' class='sunct'>";
			  if($lastID==$ID){
				    $this->LogicListAllSunType($ID,"　");
			  }
			  echo "</div>\r\n";
			}//没权限栏目
			else{
				 $sonNeedShow = false;
		  	 $this->dsql->SetQuery("Select ID From #@__arctype where reID={$ID}");
		     $this->dsql->Execute('ss');
		     while($srow=$this->dsql->GetArray('ss')){
		        	if( in_array($srow['ID'],$this->aChannels) ){ $sonNeedShow = true;  break; }
		     }
		  	 //如果二级栏目中有的所属归类文档
		  	 if($sonNeedShow===true){
			      echo "<dl class='topcc'>\r\n";
			      echo "  <dd class='dlf'><img style='cursor:hand' onClick=\"LoadSuns('suns{$ID}',{$ID});\" src='img/tree_explode.gif' width='11' height='11'></dd>\r\n";
			      echo "  <dd class='dlr'>{$typeName}</dd>\r\n";
			      echo "</dl>\r\n";
			      echo "<div id='suns".$ID."' class='sunct'>";
			      $this->LogicListAllSunType($ID,"　",true);
			      echo "</div>\r\n";
		  	 }
			}
		}
	}
	//获得子类目的递归调用
	function LogicListAllSunType($ID,$step,$needcheck=true)
	{
		$fid = $ID;
		$this->dsql->SetQuery("Select ID,reID,typedir,typename,ispart,channeltype From #@__arctype where reID='".$ID."' And ispart<>3 order by sortrank");
		$this->dsql->Execute($fid);
		if($this->dsql->GetTotalRow($fid)>0)
		{
		  
		  while($row=$this->dsql->GetObject($fid))
		  {
			  $typeDir = $row->typedir;
			  $typeName = $row->typename;
			  $reID = $row->reID;
			  $ID = $row->ID;
			  $ispart = $row->ispart;
			  $channeltype = $row->channeltype;
			  if($step=="　") $stepdd = 2;
			  else $stepdd = 3;
			  
			  //有权限栏目
			  if(in_array($ID,$this->aChannels) || $needcheck===false || $this->isAdminAll===true)
			  {
			  
			     //互动栏目
			     if($channeltype<-1){
			     	 $smenu = " oncontextmenu=\"CommonMenuWd(this,$ID,'".urlencode($typeName)."')\"";
			     	 $timg = " <img src='img/tree_list.gif'> ";
			     }
			     //普通列表
			     else if($ispart==0){
			  	   $smenu = " oncontextmenu=\"CommonMenu(this,$ID,'".urlencode($typeName)."')\"";
			  	   $timg = " <img src='img/tree_list.gif'> ";
			     }
			     //带封面的频道
			     else if($ispart==1)
			     {
			  	   $timg = " <img src='img/tree_part.gif'> ";
			  	   $smenu = " oncontextmenu=\"CommonMenuPart(this,$ID,'".urlencode($typeName)."')\"";
			     }
			     //独立页面
			     else if($ispart==2){
			  	   $timg = " <img src='img/tree_page.gif'> ";
			  	   $smenu = " oncontextmenu=\"SingleMenu(this,$ID,'".urlencode($typeName)."')\"";
			     }
			     //跳转
			     else if($ispart==3){
			  	   $timg = " <img src='img/tree_page.gif'> ";
			  	   $smenu = " ";
			     }
			     
			     echo "  <table class='sunlist'>\r\n";
			     echo "   <tr>\r\n";
			     echo "     <td>".$step.$timg."<a href='catalog_do.php?cid=".$ID."&dopost=listArchives'{$smenu}>".$typeName."</a></td>\r\n";
			     echo "   </tr>\r\n";
			     echo "  </table>\r\n";
			  
			     $this->LogicListAllSunType($ID,$step."　",false);
			  }
		  }
		}
	}
	//------------------------------------------------------
	//-----返回与某个目相关的下级目录的类目ID列表(删除类目或文章时调用)
	//------------------------------------------------------
	function GetSunTypes($ID,$channel=0)
	{
		$this->idArray[$this->idCounter]=$ID;
		$this->idCounter++;
		$fid = $ID;
	  if($channel!=0) $csql = " And channeltype=$channel ";
	  else $csql = "";
		$this->dsql->SetQuery("Select ID From #@__arctype where reID=$ID $csql");
		$this->dsql->Execute("gs".$fid);
    //if($this->dsql->GetTotalRow("gs".$fid)!=0)
		//{
		while($row=$this->dsql->GetObject("gs".$fid)){
			$nid = $row->ID;
			$this->GetSunTypes($nid,$channel);
		}
		//}
		return $this->idArray;
	}
	//----------------------------------------------------------------------------
	//获得某ID的下级ID(包括本身)的SQL语句“($tb.typeid=id1 or $tb.typeid=id2...)”
	//----------------------------------------------------------------------------
	function GetSunID($ID,$tb="#@__archives",$channel=0)
	{
		$this->sunID = "";
		$this->idCounter = 0;
		$this->idArray = "";
		$this->GetSunTypes($ID,$channel);
		$this->dsql->Close();
		$this->dsql = 0;
		$rquery = "";
		for($i=0;$i<$this->idCounter;$i++)
		{
			if($i!=0) $rquery .= " Or ".$tb.".typeid='".$this->idArray[$i]."' ";
			else      $rquery .= "    ".$tb.".typeid='".$this->idArray[$i]."' ";
		}
		reset($this->idArray);
		$this->idCounter = 0;
		return " (".$rquery.") ";
	}
}
?>