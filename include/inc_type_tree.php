<?php 
//class TypeTree
//目录树(用于选择栏目)
//--------------------------------
require_once(dirname(__FILE__)."/config_base.php");
class TypeTree
{
	var $dsql;
	var $artDir;
	var $baseDir;
	var $idCounter;
	var $idArrary;
	var $shortName;
	
	//-------------
	//php5构造函数
	//-------------
	function __construct()
 	{
		$this->idCounter = 0;
		$this->artDir = $GLOBALS['cfg_cmspath'].$GLOBALS['cfg_arcdir'];
		$this->baseDir = $GLOBALS['cfg_basedir'];
		$this->shortName = $GLOBALS['art_shortname'];
		$this->idArrary = "";
		$this->dsql = 0;
  }
	function TypeTree()
	{
		$this->__construct();
	}
	//------------------
	//清理类
	//------------------
	function Close()
	{
		if(is_object($this->dsql)){ $this->dsql->Close(); @$this->dsql=0; }
		$this->idArrary = "";
		$this->idCounter = 0;
	}
	//
	//----读出所有分类,在类目管理页(list_type)中使用----------
	//
	function ListAllType($nowdir=0,$opall=false,$channelid=0)
	{
		if($this->dsql==0){ $this->dsql = new DedeSql(); }
		
		$this->dsql->SetQuery("Select ID,typedir,typename,ispart,channeltype From #@__arctype where reID=0 order by sortrank");
		$this->dsql->Execute(0);
		
		$lastID = GetCookie('lastCidTree');
		
		while($row=$this->dsql->GetObject(0))
		{	 
			$typeDir = $row->typedir;
			$typeName = $row->typename;
			$ispart = $row->ispart;
			$ID = $row->ID;
			$dcid = $row->channeltype;
			
			if($ispart==2) continue;
			
			//普通列表
			if($ispart==0 || ($ispart==1 && $opall)){
				if($channelid==0 || $channelid==$dcid) $smenu = " <input type='checkbox' name='selid' id='selid$ID' class='np' onClick=\"ReSel($ID,'$typeName')\"> ";
			  else $smenu = "[×]";
			}
			//带封面的频道
			else if($ispart==1) $smenu = "[封面]";
			
			if($channelid>0) $dcid = $channelid;
			else $dcid = 0;
			
			echo "<dl class='topcc'>\r\n";
			echo "  <dd class='dlf'><img style='cursor:hand' onClick=\"LoadSuns('suns{$ID}',{$ID},{$dcid});\" src='img/tree_explode.gif' width='11' height='11'></dd>\r\n";
			echo "  <dd class='dlr'>$typeName{$smenu}</dd>\r\n";
			echo "</dl>\r\n";
			echo "<div id='suns".$ID."' class='sunct'>";
			if($lastID==$ID){
				 $this->LogicListAllSunType($ID,"　",$opall,$channelid);
			}
			echo "</div>\r\n";
		}
	}
	//获得子类目的递归调用
	function LogicListAllSunType($ID,$step,$opall,$channelid)
	{
		$fid = $ID;
		$this->dsql->SetQuery("Select ID,reID,typedir,typename,ispart,channeltype From #@__arctype where reID='".$ID."' order by sortrank");
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
			  if($step=="　") $stepdd = 2;
			  else $stepdd = 3;
			  $dcid = $row->channeltype;
			  
			  if($ispart==2) continue;
			  
			  //普通列表
			  if($ispart==0||($ispart==1 && $opall)){
			  	if($channelid==0 || $channelid==$dcid) $smenu = " <input type='checkbox' name='selid' id='selid$ID' class='np' onClick=\"ReSel($ID,'$typeName')\"> ";
			  	else $smenu = "[×]";
			  	$timg = " <img src='img/tree_list.gif'> ";
			  }
			  //带封面的频道
			  else if($ispart==1){
			  	$timg = " <img src='img/tree_part.gif'> ";
			  	$smenu = "[封面]";
			  }
			  
			  echo "  <table class='sunlist'>\r\n";
			  echo "   <tr>\r\n";
			  echo "     <td>".$step.$timg.$typeName."{$smenu}</td>\r\n";
			  echo "   </tr>\r\n";
			  echo "  </table>\r\n";
			  
			  $this->LogicListAllSunType($ID,$step."　",$opall,$channelid);
		  }
		}
	}
}
?>