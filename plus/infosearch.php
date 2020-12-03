<?php
require_once(dirname(__FILE__)."/../include/config_base.php");
require_once(dirname(__FILE__)."/../include/pub_datalist_dm.php");

$dsql = new dedesql();
$dsql->setquery("select id, name from #@__area where reid=0");
$dsql->execute();
$areadata = '<option value="0" > -不限- </option>';
while($row = $dsql->getarray())
{
	$areadata .= '<option value="'.$row['id'].'">'.$row['name']."</option>\n";
}

$hotinfos = array();
$dsql->setquery("select ID, typeid, title from #@__infos order by click desc limit 10" );
$dsql->execute();
while($row = $dsql->getarray())
{
	$hotinfos[] = $row;
}
$wheresql = '';
$areaid = intval($areaid);
$areaid = $areaid < 1 ? 0 : $areaid;
$q = trim($q);
if($areaid > 0) {
	$wheresql = "areaid=$areaid and ";
}

$query = "select ID,typeid,title,memberID,writer,senddate from #@__infos where $wheresql title like '%$q%' order by senddate desc";


$dlist = new DataList();


$dlist->pageSize = 20;
$dlist->SetParameter("q",$q);
$dlist->SetParameter("action",'search');
$dlist->SetParameter("areaid",$areaid);
$dlist->SetSource($query);
include(dirname(__FILE__)."/../templets/default/infosearch.htm");
$dlist->Close();

?>