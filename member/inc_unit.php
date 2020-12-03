<?
//-----------------------------
//--获得允许投稿的类别列表
//-----------------------------
function GetOptionArray($openid=0,$dsql)
{
  if($openid!=0){
	  $row = $dsql->GetOne("Select ID,typename From #@__arctype where ID='$openid'");
	  echo "<option value='".$row['ID']."'>".$row['typename']."</option>\r\n";
  }
  $query = "Select ID,typename From #@__arctype where reID=0 And issend=1 And channeltype=1 And ispart<>2";
  $dsql->SetQuery($query);
  $dsql->Execute();
  while($row=$dsql->GetObject()){
    echo "<option value='".$row->ID."'>".$row->typename."</option>\r\n";
    GetSunOptionArray($row->ID,"─",$dsql);
  }
}
function GetSunOptionArray($ID,$step,$dsql)
{
	$dsql->SetQuery("Select ID,typename From #@__arctype where reID='".$ID."' And issend=1 And channeltype=1 And ispart<>2");
	$dsql->Execute($ID);
	while($row=$dsql->GetObject($ID))
  {
     echo "<option value='".$row->ID."'>$step".$row->typename."</option>\r\n";
     GetSunOptionArray($row->ID,$step."─",$dsql);
  }
}
?>