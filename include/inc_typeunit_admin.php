<?php
//class TypeUnit
//这个类主要是封装频道管理时的一些复杂操作
//--------------------------------
require_once(dirname(__FILE__)."/config_base.php");
require_once(dirname(__FILE__)."/inc_channel_unit_functions.php");
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
	var $CatalogNums;
	//-------------
	//php5构造函数
	//-------------
	function __construct($catlogs=''){
		$this->idCounter = 0;
		$this->artDir = $GLOBALS['cfg_cmspath'].$GLOBALS['cfg_arcdir'];
		$this->baseDir = $GLOBALS['cfg_basedir'];
		$this->shortName = $GLOBALS['art_shortname'];
		$this->idArrary = "";
		$this->dsql = new DedeSql(false);
		$this->aChannels = Array();
		$this->isAdminAll = false;
		if(!empty($catlogs) && $catlogs!='-1'){
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
	function Close(){
		if(is_object($this->dsql)){
			@$this->dsql->Close();
			unset($this->dsql);
		}
		$this->idArrary = "";
		$this->idCounter = 0;
	}
	//------------------------------
	function GetTotalArc($tid){
		return $this->GetCatalogNum($tid);
	}
	//
	//获取所有栏目的文档ID数
	//
	function UpdateCatalogNum()
	{
		$this->dsql->SetQuery("SELECT typeid,count(typeid) as dd FROM `#@__full_search` group by typeid");
		$this->dsql->Execute();
		while($row = $this->dsql->GetArray()){
			$this->CatalogNums[$row['typeid']] = $row['dd'];
		}
	}
	function GetCatalogNum($tid)
	{
		if(!is_array($this->CatalogNums)){ $this->UpdateCatalogNum(); }
		if(!isset($this->CatalogNums[$tid])) return 0;
		else
		{
			$totalnum = 0;
			$GLOBALS['idArray'] = array();
			$ids = TypeGetSunTypes($tid,$this->dsql,0);
			foreach($ids as $tid){
				if(isset($this->CatalogNums[$tid])) $totalnum += $this->CatalogNums[$tid];
			}
			return $totalnum;
		}
	}
	//
	//----读出所有分类,在类目管理页(list_type)中使用----------
	//
	function ListAllType($channel=0,$nowdir=0)
	{

		$this->dsql->SetQuery("Select ID,typedir,typename,ispart,sortrank,ishidden,channeltype From #@__arctype where reID=0 order by sortrank");
		$this->dsql->Execute('pn0');

		$lastID = GetCookie('lastCid');

		while($row=$this->dsql->GetObject('pn0'))
		{
			$typeDir = $row->typedir;
			$typeName = $row->typename;
			$ispart = $row->ispart;
			$ID = $row->ID;
			$rank = $row->sortrank;
			$channeltype = $row->channeltype;
			if($row->ishidden=='1') $nss = "<font color='red'>[隐]</font>";
			else  $nss = "";

			//有权限栏目
			if($this->isAdminAll===true || in_array($ID,$this->aChannels))
			{
			   //print_r($this->aChannels);
			   //互动栏目
			   if($channeltype<-1)
			   {
			   	 echo "<table width='96%' border='0' cellpadding='1' cellspacing='0' align='center' style='margin:0px auto' class='tblist2'>\r\n";
				 echo "<tr align='center' oncontextmenu=\"CommonMenuWd(this,$ID,'".urlencode($typeName)."')\">\r\n";
				 echo "<td width='7%'><input class='np' type='checkbox' name='tids[]' value='{$ID}'></td>\r\n";
				 echo "<td width='6%'>[ID:".$ID."]</td>\r\n";
				 echo "<td width='27%' align='left'>\r\n<img onClick=\"LoadSuns('suns".$ID."',$ID);\" src='images/class_sopen.gif' width='11' height='15' border='0' align='absmiddle' />  <a href='catalog_do.php?cid=".$ID."&dopost=listArchives' style='font-size:14px; text-decoration:none;'>{$nss}".$typeName."</a><font color='red'>[互]</font> </td>\r\n";
				 echo "<td width='10%'>(文档：".$this->GetTotalArc($ID).")</td>\r\n";
				 echo "<td width='8%'>$channeltype</td>\r\n";
				 echo "<td width='34%' align='right' style='letter-spacing:1px;'>\r\n";
				 echo "<a href='{$GLOBALS['cfg_plus_dir']}/list.php?tid={$ID}' target='_blank'>预览</a>\r\n";
				 echo "| <a href='catalog_do.php?cid={$ID}&dopost=listArchives'>内容</a>\r\n";
				 echo "| <a href='catalog_add.php?ID={$ID}'>添加</a>\r\n";
				 echo "| <a href='catalog_edit.php?ID={$ID}'>修改</a>\r\n";
				 echo "| <a href='catalog_move.php?job=movelist&typeid={$ID}'>移动</a>\r\n";
				 echo "| <a href='catalog_del.php?ID={$ID}&typeoldname=".urlencode($typeName)."'>删除</a>\r\n";
				 echo "</td><td width='8%'><label>";
				 echo "<input name='sortrank{$ID}' type='text' id='textfield2' value='{$rank}' size='2' maxlength='4' style='text-align:center;' />";
				 echo "</label></td>\r\n</tr>\r\n";
			     echo "</table>\r\n <div id='suns".$ID."'>\r\n";

			   }
			   //普通列表
			   else if($ispart==0)
			   {
			   	 echo "<table width='96%' border='0' cellpadding='1' cellspacing='0' align='center' style='margin:0px auto' class='tblist2'>\r\n";
				 echo "<tr align='center' oncontextmenu=\"CommonMenu(this,$ID,'".urlencode($typeName)."')\">\r\n";
				 echo "<td width='7%'><input class='np' type='checkbox' name='tids[]' value='{$ID}'></td>\r\n";
				 echo "<td width='6%'>[ID:".$ID."]</td>\r\n";
				 echo "<td width='27%' align='left'><img style='cursor:hand' onClick=\"LoadSuns('suns".$ID."',$ID);\" src='images/class_sopen.gif' width='11' height='15' border='0' align='absmiddle' />  <a href='catalog_do.php?cid=".$ID."&dopost=listArchives' style='font-size:14px; text-decoration:none;'>{$nss}".$typeName."</a></td>\r\n";
				 echo "<td width='10%'>(文档：".$this->GetTotalArc($ID).")</td>\r\n";
				 echo "<td width='8%'>$channeltype</td>\r\n";
				 echo "<td width='34%' align='right' style='letter-spacing:1px;'>\r\n";
				 echo "<a href='{$GLOBALS['cfg_plus_dir']}/list.php?tid={$ID}' target='_blank'>预览</a>\r\n";
				 echo "| <a href='catalog_do.php?cid={$ID}&dopost=listArchives'>内容</a>\r\n";
				 echo "| <a href='catalog_add.php?ID={$ID}'>添加</a>\r\n";
				 echo "| <a href='catalog_edit.php?ID={$ID}'>修改</a>\r\n";
				 echo "| <a href='catalog_move.php?job=movelist&typeid={$ID}'>移动</a>\r\n";
				 echo "| <a href='catalog_del.php?ID={$ID}&typeoldname=".urlencode($typeName)."'>删除</a>\r\n";
				 echo "</td>\r\n<td width='8%'><label>";
				 echo "<input name='sortrank{$ID}' type='text' id='textfield2' value='{$rank}' size='2' maxlength='4' style='text-align:center;' />";
				 echo "</label></td>\r\n</tr>\r\n";
			     echo "</table>\r\n <div id='suns".$ID."'>\r\n";

			   }
			   //带封面的频道
			   else if($ispart==1)
	       {
			   	 echo "<table width='96%' border='0' cellpadding='1' cellspacing='0' align='center' style='margin:0px auto' class='tblist2'>\r\n";
				 echo "<tr align='center' oncontextmenu=\"CommonMenuPart(this,$ID,'".urlencode($typeName)."')\">\r\n";
				 echo "<td width='7%'><input class='np' type='checkbox' name='tids[]' value='{$ID}'></td>\r\n";
				 echo "<td width='6%'>[ID:".$ID."]</td>\r\n";
				 echo "<td width='27%' align='left'><img style='cursor:hand' onClick=\"LoadSuns('suns".$ID."',$ID);\" src='images/class_sopen.gif' width='11' height='15' border='0' align='absmiddle' />  <a href='catalog_do.php?cid=".$ID."&dopost=listArchives' style='font-size:14px; text-decoration:none;'>{$nss}".$typeName."</a></td>\r\n";
				 echo "<td width='10%'>(文档：".$this->GetTotalArc($ID).")</td>\r\n";
				 echo "<td width='8%'>$channeltype</td>\r\n";
				 echo "<td width='34%' align='right' style='letter-spacing:1px;'>\r\n";
				 echo "<a href='{$GLOBALS['cfg_plus_dir']}/list.php?tid={$ID}' target='_blank'>预览</a>\r\n";
				 echo "| <a href='catalog_do.php?cid={$ID}&dopost=listArchives'>内容</a>\r\n";
				 echo "| <a href='catalog_add.php?ID={$ID}'>添加</a>\r\n";
				 echo "| <a href='catalog_edit.php?ID={$ID}'>修改</a>\r\n";
				 echo "| <a href='catalog_move.php?job=movelist&typeid={$ID}'>移动</a>\r\n";
				 echo "| <a href='catalog_del.php?ID={$ID}&typeoldname=".urlencode($typeName)."'>删除</a>\r\n";
				 echo "</td>\r\n<td width='8%'><label>";
				 echo "<input name='sortrank{$ID}' type='text' id='textfield2' value='{$rank}' size='2' maxlength='4' style='text-align:center;' />";
				 echo "</label></td>\r\n</tr>\r\n";
			     echo "</table>\r\n <div id='suns".$ID."'>\r\n";
				 }
			  //独立页面
			  else if($ispart==2)
			  {
			   	 echo "<table width='96%' border='0' cellpadding='1' cellspacing='0' align='center' style='margin:0px auto' class='tblist2'>\r\n";
				 echo "<tr align='center' oncontextmenu=\"CommonMenuPart(this,$ID,'".urlencode($typeName)."')\">\r\n";
				 echo "<td width='7%'><input class='np' type='checkbox' name='tids[]' value='{$ID}'></td>\r\n";
				 echo "<td width='6%'>[ID:".$ID."]</td>\r\n";
				 echo "<td width='27%' align='left'><img style='cursor:hand' onClick=\"LoadSuns('suns".$ID."',$ID);\" src='images/class_sopen.gif' width='11' height='15' border='0' align='absmiddle' />  <a href='catalog_do.php?cid=".$ID."&dopost=listArchives' style='font-size:14px; text-decoration:none;'>{$nss}".$typeName."</a></td>\r\n";
				 echo "<td width='10%'>(文档：".$this->GetTotalArc($ID).")</td>\r\n";
				 echo "<td width='8%'>独立页</td>\r\n";
				 echo "<td width='34%' align='right' style='letter-spacing:1px;'>\r\n";
				 echo "<a href='{$GLOBALS['cfg_plus_dir']}/list.php?tid={$ID}' target='_blank'>预览</a>\r\n";
				 echo "| <a href='catalog_do.php?cid={$ID}&dopost=listArchives'>内容</a>\r\n";
				 echo "| <a href='catalog_add.php?ID={$ID}'>添加</a>\r\n";
				 echo "| <a href='catalog_edit.php?ID={$ID}'>修改</a>\r\n";
				 echo "| <a href='catalog_move.php?job=movelist&typeid={$ID}'>移动</a>\r\n";
				 echo "| <a href='catalog_del.php?ID={$ID}&typeoldname=".urlencode($typeName)."'>删除</a>\r\n";
				 echo "</td><td width='8%'><label>";
				 echo "<input name='sortrank{$ID}' type='text' id='textfield2' value='{$rank}' size='2' maxlength='4' style='text-align:center;' />";
				 echo "</label></td>\r\n</tr>\r\n";
			     echo "</table>\r\n <div id='suns".$ID."'>\r\n";
				 }

			  if($lastID==$ID){
				   $this->LogicListAllSunType($ID,"　",false);
			  }
		  }
		  //没权限栏目
		  else{
		  	 $sonNeedShow = false;
		  	 $this->dsql->SetQuery("Select ID From #@__arctype where reID={$ID}");
		     $this->dsql->Execute('ss');
		     while($srow=$this->dsql->GetArray('ss')){
		        	if( in_array($srow['ID'],$this->aChannels) ){ $sonNeedShow = true;  break; }
		     }
		  	 //如果二级栏目中有的所属归类文档
		  	 if($sonNeedShow===true)
		  	 {
		  	    //互动栏目
			      if($channeltype<-1)
			      {
				 echo "<table width='96%' border='0' cellpadding='1' cellspacing='0' align='center' style='margin:0px auto' class='tblist2'>\r\n";
				 echo "<tr align='center'>";
				 echo "<td width='7%'></td>";
				 echo "<td width='6%'>[ID:".$ID."]</td>";
				 echo "<td width='27%' align='left'><img style='cursor:hand' onClick=\"LoadSuns('suns".$ID."',$ID);\" src='images/class_sopen.gif' width='11' height='15' border='0' align='absmiddle' />  <a href='catalog_do.php?cid=".$ID."&dopost=listArchives' style='font-size:14px; text-decoration:none;'>{$nss}".$typeName."</a></td>";
				 echo "<td width='10%'>(文档：".$this->GetTotalArc($ID).")</td>";
				 echo "<td width='8%'>$channeltype</td>\r\n";
				 echo "<td width='34%' align='right' style='letter-spacing:1px;'>";
				 echo "</td><td width='8%'><label>";
				 echo "<input name='sortrank{$ID}' type='text' id='sortrank{$ID}' value='{$rank}' size='2' maxlength='4' style='text-align:center;' />";
				 echo "</label></td></tr>";
			     echo "</table>\r\n <div id='suns".$ID."'>";
			      }
				  //普通列表
				  else if($ispart==0)
			      {
				 echo "<table width='96%' border='0' cellpadding='1' cellspacing='0' align='center' style='margin:0px auto' class='tblist2'>\r\n";
				 echo "<tr align='center'>";
				 echo "<td width='7%'></td>";
				 echo "<td width='6%'>[ID:".$ID."]</td>";
				 echo "<td width='27%' align='left'><img style='cursor:hand' onClick=\"LoadSuns('suns".$ID."',$ID);\" src='images/class_sopen.gif' width='11' height='15' border='0' align='absmiddle' />  <a href='catalog_do.php?cid=".$ID."&dopost=listArchives' style='font-size:14px; text-decoration:none;'>{$nss}".$typeName."</a></td>";
				 echo "<td width='10%'>(文档：".$this->GetTotalArc($ID).")</td>";
				 echo "<td width='10%'>$channeltype</td>\r\n";
				 echo "<td width='34%' align='right' style='letter-spacing:1px;'>";
				 echo "</td><td width='10%'><label>";
				 echo "<input name='sortrank{$ID}' type='text' id='sortrank{$ID}' value='{$rank}' size='2' maxlength='4' style='text-align:center;' />";
				 echo "</label></td></tr>";
			     echo "</table>\r\n <div id='suns".$ID."'>";
			      }
				  //带封面的频道
				  else if($ispart==1)
	          {
				 echo "<table width='96%' border='0' cellpadding='1' cellspacing='0' align='center' style='margin:0px auto' class='tblist2'>\r\n";
				 echo "<tr align='center'>";
				 echo "<td width='7%'></td>";
				 echo "<td width='6%'>[ID:".$ID."]</td>";
				 echo "<td width='27%' align='left'><img style='cursor:hand' onClick=\"LoadSuns('suns".$ID."',$ID);\" src='images/class_sopen.gif' width='11' height='15' border='0' align='absmiddle' />  <a href='catalog_do.php?cid=".$ID."&dopost=listArchives' style='font-size:14px; text-decoration:none;'>{$nss}".$typeName."</a></td>";
				 echo "<td width='10%'>(文档：".$this->GetTotalArc($ID).")</td>";
				 echo "<td width='10%'>$channeltype</td>\r\n";
				 echo "<td width='34%' align='right' style='letter-spacing:1px;'>";
				 echo "</td><td width='10%'><label>";
				 echo "<input name='sortrank{$ID}' type='text' id='sortrank{$ID}' value='{$rank}' size='2' maxlength='4' style='text-align:center;' />";
				 echo "</label></td></tr>";
			     echo "</table>\r\n <div id='suns".$ID."'>";
			     }
				 //独立页面
				 else if($ispart==2)
				 {
				 echo "<table width='96%' border='0' cellpadding='1' cellspacing='0' align='center' style='margin:0px auto' class='tblist2'>\r\n";
				 echo "<tr align='center'>";
				 echo "<td width='7%'></td>";
				 echo "<td width='6%'>[ID:".$ID."]</td>";
				 echo "<td width='27%' align='left'><img style='cursor:hand' onClick=\"LoadSuns('suns".$ID."',$ID);\" src='images/class_sopen.gif' width='11' height='15' border='0' align='absmiddle' />  <a href='catalog_do.php?cid=".$ID."&dopost=listArchives' style='font-size:14px; text-decoration:none;'>{$nss}".$typeName."</a></td>";
				 echo "<td width='10%'>(文档：".$this->GetTotalArc($ID).")</td>";
				 echo "<td width='10%'>独立页</td>\r\n";
				 echo "<td width='34%' align='right' style='letter-spacing:1px;'>";
				 echo "</td><td width='10%'><label>";
				 echo "<input name='sortrank{$ID}' type='text' id='textfield2' value='{$rank}' size='2' maxlength='4' style='text-align:center;' />";
				 echo "</label></td></tr>";
			     echo "</table>\r\n <div id='suns".$ID."'>";
			     }
			     $this->LogicListAllSunType($ID,"　",true);
			  }
		  }
			echo "</div>\r\n";
		}
	}
	//获得子类目的递归调用
	function LogicListAllSunType($ID,$step,$needcheck=true)
	{
		$fid = $ID;
		$this->dsql->SetQuery("Select ID,reID,typedir,typename,ispart,sortrank,ishidden,channeltype From #@__arctype where reID='".$ID."' order by sortrank");
		$this->dsql->Execute('s'.$fid);
		while($row=$this->dsql->GetObject('s'.$fid))
		{
			  $typeDir = $row->typedir;
			  $typeName = $row->typename;
			  $reID = $row->reID;
			  $ID = $row->ID;
			  $ispart = $row->ispart;
			  $channeltype = $row->channeltype;
			  if($step=="  ") $stepdd = 2;
			  else $stepdd = 3;
			  $rank = $row->sortrank;
			  if($row->ishidden=='1') $nss = "<font color='red'>[隐]</font>";
			  else  $nss = "";
			  //有权限栏目
			  if(in_array($ID,$this->aChannels) || $needcheck===false || $this->isAdminAll===true)
			  {
			  	 //互动栏目
			   if($channeltype<-1)
			    {
				 echo "<table width='96%' border='0' cellspacing='1' cellpadding='0' align='center' style='margin:0px auto' class='tblist2'>\r\n";
				 echo "<tr align='center' class='trlbg' oncontextmenu=\"CommonMenuWd(this,$ID,'".urlencode($typeName)."')\">\r\n";
				 echo "<td width='7%'><input class='np' type='checkbox' name='tids[]' value='{$ID}'></td>";
				 echo "<td width='6%'>[ID:".$ID."]</td>";
				 echo "<td width='27%' align='left'>$step   ├  <a href='catalog_do.php?cid=".$ID."&dopost=listArchives' style='font-size:14px; text-decoration:none;'>{$nss}".$typeName."</a><font color='red'>[互]</font> </td>";
				 echo "<td width='10%'>(文档：".$this->GetTotalArc($ID).")</td>";
				 echo "<td width='8%'>$channeltype</td>\r\n";
				 echo "<td width='34%' align='right' style='letter-spacing:1px;'>";
				 echo "<a href='{$GLOBALS['cfg_plus_dir']}/list.php?tid={$ID}' target='_blank'>预览</a> ";
				 echo "| <a href='catalog_do.php?cid={$ID}&dopost=listArchives'>列出</a> ";
				 echo "| <a href='catalog_add.php?ID={$ID}'>添加</a> ";
				 echo "| <a href='catalog_edit.php?ID={$ID}'>修改</a> ";
				 echo "| <a href='catalog_move.php?job=movelist&typeid={$ID}'>移动</a> ";
				 echo "| <a href='catalog_del.php?ID={$ID}&typeoldname=".urlencode($typeName)."'>删除</a> ";
				 echo "</td><td width='8%'><label>";
				 echo "<input name='sortrank{$ID}' type='text' id='textfield2' value='{$rank}' size='2' maxlength='4' style='text-align:center;' />";
				 echo "</label></td></tr>";
				 echo "    </table>\r\n";
			    }
			    //普通列表
			    else if($ispart==0)
			    {
				 echo "<table width='96%' border='0' cellspacing='1' cellpadding='0' align='center' style='margin:0px auto' class='tblist2'>\r\n";
				 echo "<tr align='center' class='trlbg'  oncontextmenu=\"CommonMenu(this,$ID,'".urlencode($typeName)."')\">\r\n";
				 echo "<td width='7%'><input class='np' type='checkbox' name='tids[]' value='{$ID}'></td>";
				 echo "<td width='6%'>[ID:".$ID."]</td>";
				 echo "<td width='27%' align='left'>$step   ├  <a href='catalog_do.php?cid=".$ID."&dopost=listArchives' style='font-size:14px; text-decoration:none;'>{$nss}".$typeName."</a> </td>";
				 echo "<td width='10%'>(文档：".$this->GetTotalArc($ID).")</td>";
				 echo "<td width='8%'>$channeltype</td>\r\n";
				 echo "<td width='34%' align='right' style='letter-spacing:1px;'>";
				 echo "<a href='{$GLOBALS['cfg_plus_dir']}/list.php?tid={$ID}' target='_blank'>预览</a> ";
				 echo "| <a href='catalog_do.php?cid={$ID}&dopost=listArchives'>内容</a> ";
				 echo "| <a href='catalog_add.php?ID={$ID}'>添加</a> ";
				 echo "| <a href='catalog_edit.php?ID={$ID}'>修改</a> ";
				 echo "| <a href='catalog_move.php?job=movelist&typeid={$ID}'>移动</a> ";
				 echo "| <a href='catalog_del.php?ID={$ID}&typeoldname=".urlencode($typeName)."'>删除</a> ";
				 echo "</td><td width='8%'><label>";
				 echo "<input name='sortrank{$ID}' type='text' id='textfield2' value='{$rank}' size='2' maxlength='4' style='text-align:center;' />";
				 echo "</label></td></tr>";
				 echo "    </table>\r\n";

			    }
			    //封面频道
			    else if($ispart==1)
			    {
				 echo "<table width='96%' border='0' cellspacing='1' cellpadding='0' align='center' style='margin:0px auto' class='tblist2'>\r\n";
				 echo "<tr align='center' class='trlbg' oncontextmenu=\"CommonMenuPart(this,$ID,'".urlencode($typeName)."')\">\r\n";
				 echo "<td width='7%'><input class='np' type='checkbox' name='tids[]' value='{$ID}'></td>";
				 echo "<td width='6%'>[ID:".$ID."]</td>";
				 echo "<td width='27%' align='left'>$step   ├  <a href='catalog_do.php?cid=".$ID."&dopost=listArchives' style='font-size:14px; text-decoration:none;'>{$nss}".$typeName."</a><font color='red'>[封面]</font></td>";
				 echo "<td width='10%'>(文档：".$this->GetTotalArc($ID).")</td>";
				 echo "<td width='8%'>$channeltype</td>\r\n";
				 echo "<td width='34%' align='right' style='letter-spacing:1px;'>";
				 echo "<a href='{$GLOBALS['cfg_plus_dir']}/list.php?tid={$ID}' target='_blank'>预览</a> ";
				 echo "| <a href='catalog_do.php?cid={$ID}&dopost=listArchives'>列出</a> ";
				 echo "| <a href='catalog_add.php?ID={$ID}'>添加</a> ";
				 echo "| <a href='catalog_edit.php?ID={$ID}'>修改</a> ";
				 echo "| <a href='catalog_move.php?job=movelist&typeid={$ID}'>移动</a> ";
				 echo "| <a href='catalog_del.php?ID={$ID}&typeoldname=".urlencode($typeName)."'>删除</a> ";
				 echo "</td><td width='8%'><label>";
				 echo "<input name='sortrank{$ID}' type='text' id='textfield2' value='{$rank}' size='2' maxlength='4' style='text-align:center;' />";
				 echo "</label></td></tr>";
				 echo "    </table>\r\n";
			    }
			    //独立页面
			    else if($ispart==2)
			    {
				 echo "<table width='96%' border='0' cellspacing='1' cellpadding='0' align='center' style='margin:0px auto' class='tblist2'>\r\n";
				 echo "<tr align='center' class='trlbg' oncontextmenu=\"SingleMenu(this,$ID,'".urlencode($typeName)."')\">\r\n";
				 echo "<td width='7%'><input class='np' type='checkbox' name='tids[]' value='{$ID}'></td>";
				 echo "<td width='6%'>[ID:".$ID."]</td>";
				 echo "<td width='27%' align='left'>$step   ├  <a href='catalog_do.php?cid=".$ID."&dopost=listArchives' style='font-size:14px; text-decoration:none;'>{$nss}".$typeName."</a><font color='red'>[封面]</font></td>";
				 echo "<td width='10%'>(文档：".$this->GetTotalArc($ID).")</td>";
				 echo "<td width='8%'>独立页</td>\r\n";
				 echo "<td width='34%' align='right' style='letter-spacing:1px;'>";
				 echo "<a href='{$GLOBALS['cfg_plus_dir']}/list.php?tid={$ID}' target='_blank'>预览</a> ";
				 echo "| <a href='catalog_do.php?cid={$ID}&dopost=listArchives'>内容</a> ";
				 echo "| <a href='catalog_add.php?ID={$ID}'>添加</a> ";
				 echo "| <a href='catalog_edit.php?ID={$ID}'>修改</a> ";
				 echo "| <a href='catalog_move.php?job=movelist&typeid={$ID}'>移动</a> ";
				 echo "| <a href='catalog_del.php?ID={$ID}&typeoldname=".urlencode($typeName)."'>删除</a> ";
				 echo "</td><td width='8%'><label>";
				 echo "<input name='sortrank{$ID}' type='text' id='textfield2' value='{$rank}' size='2' maxlength='4' style='text-align:center;' />";
				 echo "</label></td></tr>";
			     echo "    </table>\r\n";
				}
			  $this->LogicListAllSunType($ID,$step."　",false);
		  }//if 有权限
		}//End while
	}
	//------------------------------------------------------
	//-----返回与某个目相关的下级目录的类目ID列表(删类目或文章时调用)
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
		while($row=$this->dsql->GetObject("gs".$fid)){
			$nid = $row->ID;
			$this->GetSunTypes($nid,$channel);
		}
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
	//----------------------------------------
	//删类目
	//----------------------------------------
	function DelType($ID,$isDelFile,$delson=true)
	{
		$this->idCounter = 0;
		$this->idArray = "";
		if($delson){
		   $this->GetSunTypes($ID);
	  }else{
	  	$this->idArray = array();
	  	$this->idArray[] = $ID;
	  }
		//删数据库里的相关记录
		foreach($this->idArray as $id)
		{
			$myrow = $this->dsql->GetOne("Select c.maintable,c.addtable From #@__arctype t left join #@__channeltype c  on c.ID=t.channeltype where t.ID='$id'",MYSQL_ASSOC);
		  if($myrow['maintable']=='') $myrow['maintable'] = '#@__archives';
		  //删数据库信息
		  $this->dsql->ExecuteNoneQuery("Delete From `{$myrow['maintable']}` where typeid='$id'");
		  if($myrow['addtable']!="") $this->dsql->ExecuteNoneQuery("Delete From `{$myrow['addtable']}` where typeid='$id'");
			$this->dsql->ExecuteNoneQuery("update `{$myrow['maintable']}` set typeid2=0 where typeid2='$id'");
			$this->dsql->ExecuteNoneQuery("Delete From `#@__spec` where typeid='$id'");
			$this->dsql->ExecuteNoneQuery("Delete From `#@__feedback` where typeid='$id'");
		  $this->dsql->ExecuteNoneQuery("Delete From `#@__arctype` where ID='$id'");
		  $this->dsql->ExecuteNoneQuery("Delete From `#@__full_search` where typeid='$id'");
		}
		@reset($this->idArray);
		$this->idCounter = 0;
		return true;
	}
	//---------------------------
	//---- 删指定目录的所有文件
	//---------------------------
	function RmDirFile($indir)
	{
   		if(!file_exists($indir)) return;
   		$dh = dir($indir);
   		while($file = $dh->read()) {
      	if($file == "." || $file == "..") continue;
      	else if(is_file("$indir/$file")) @unlink("$indir/$file");
     	 	else{
         		$this->RmDirFile("$indir/$file");
      	}
      	if(is_dir("$indir/$file")){
         	@rmdir("$indir/$file");
      	}
   		}
   		$dh->close();
   		return(1);
	}
}
?>