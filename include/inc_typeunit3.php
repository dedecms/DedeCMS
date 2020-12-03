<?
//class TypeUnit
//这个类主要是封装频道管理时的一些复杂操作 
//--------------------------------
require_once(dirname(__FILE__)."/../include/config_base.php");
class TypeUnit
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
		$this->artDir = $GLOBALS['cfg_arcdir'];
		$this->baseDir = $GLOBALS['cfg_basedir'];
		$this->shortName = $GLOBALS['art_shortname'];
		$this->idArrary = "";
		$this->dsql = 0;
  }
	function TypeUnit()
	{
		$this->__construct();
	}
	//------------------
	//清理类
	//------------------
	function Close()
	{
		if(is_object($this->dsql)){
			$this->dsql->Close();
			@$this->dsql=0;
		}
		$this->idArrary = "";
		$this->idCounter = 0;
	}
	//
	//----读出所有分类,在类目管理页(list_type)中使用----------
	//
	function ListAllType($chennel=0,$nowdir=0)
	{
		if($this->dsql==0){ $this->dsql = new DedeSql(); }
		
		if($chennel>0)
		{	$this->dsql->SetQuery("Select ID,typedir,typename,ispart From #@__arctype where ID='$chennel'");}
		else
		{	$this->dsql->SetQuery("Select ID,typedir,typename,ispart From #@__arctype where reID=0 order by sortrank"); }
		$this->dsql->Execute(0);
		
		while($row=$this->dsql->GetObject(0))
		{	 
			$typeDir = $row->typedir;
			$typeName = $row->typename;
			$ispart = $row->ispart;
			$ID = $row->ID;
			
			if($ispart==2){
				continue;
			}
			
			echo "<table width='100%' border='0' cellspacing='0' cellpadding='2'>\r\n";
			
			echo "  <tr bgcolor='#F5F5F5'>\r\n";
			echo "  <td width='2%'><img style='cursor:hand' onClick=\"showHide('suns".$ID."');\" src='img/dedeexplode.gif' width='11' height='11'></td>\r\n";
			echo "  <td  background='img/itemcomenu.gif'><a href='catalog_do.php?cid=".$ID."&dopost=listArchives'>".$typeName."</a>";
			echo "  </td></tr>\r\n";
			
			echo "  <tr id='suns".$ID."'><td colspan='2'>\r\n";
			echo "    <table width='100%' border='0' cellspacing='0' cellpadding='0'>\r\n";
			
			if($nowdir == -1) $this->LogicListAllSunType($ID,"　");
			else if($nowdir == $ID) $this->LogicListAllSunType($ID,"　");
			
			echo "    </table>\r\n</td></tr>\r\n";
			echo "</table>\r\n";
		}
	}
	//获得子类目的递归调用
	function LogicListAllSunType($ID,$step)
	{
		$fid = $ID;
		$this->dsql->SetQuery("Select ID,reID,typedir,typename,ispart From #@__arctype where reID='".$ID."' order by sortrank");
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
			  
			  if($ispart==2) continue;
			  
			  echo "    <tr height='24'>\r\n";
			  echo "    <td><a href='catalog_do.php?cid=".$ID."&dopost=listArchives'>$step ·".$typeName."</a>";
			  echo "    </td></tr>\r\n";
			  
			  $this->LogicListAllSunType($ID,$step."　");
		  }
		}
	}
	//------------------------------------------------------
	//-----返回与某个目相关的下级目录的类目ID列表(删除类目或文章时调用)
	//------------------------------------------------------
	function GetSunTypes($ID,$channel=0)
	{
		if($this->dsql==0) $this->dsql = new DedeSql(false);
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
		if($this->dsql==0) $this->dsql = new DedeSql(false);
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