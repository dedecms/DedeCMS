<?
function dtime()
{
	$dtime=strftime("%Y-%m-%d %H:%M:%S",time());
	return($dtime);
}	
function errmsg($msg)
{
	echo "<script>\n";
	echo "alert('$msg');\n";
	echo "history.go(-1);\n";
	echo "</script>\n";
}
function smail($to,$msg)
{
        $subject = "<?=$bbsname?>论坛注册会员";
        $headers = "From: $to\r\n";
        @mail("$to", "$subject", "$msg", "$headers");
}
function getpath($dtime)
{
	list($d,$s) = split(" ",$dtime);
	list($y,$m,$d) = split("-",$d);
	if(!is_dir("upimg/$y")) mkdir("upimg/$y",0777);
	if(!is_dir("upimg/$y/$m")) mkdir("upimg/$y/$m",0777);
	if(!is_dir("upimg/$y/$m/$d")) mkdir("upimg/$y/$m/$d",0777);
	return("upimg/$y/$m/$d");
}
function getname($dtime,$uID)
{
	list($d,$s) = split(" ",$dtime);
	list($y,$m,$d) = split("-",$d);
	list($h,$mm,$s) = split(":",$s);
	return("$uID$h$mm$s.jpg");
}
//
//--获得允许投稿的类别列表---------------
//
function GetOptionArray($openid=0,$conn)
{
    if($openid!=0)
    {
    	$rs = @mysql_query("Select * From dede_arttype where ID=$openid",$conn);
    	$row=mysql_fetch_object($rs);
    	echo "<option value='".$row->ID."'>".$row->typename."</option>\r\n";
    }
    $rs = @mysql_query("Select * From dede_arttype where reID=0 And issend=1 And channeltype=1",$conn);
    while($row=mysql_fetch_object($rs))
    {
          echo "<option value='".$row->ID."'>".$row->typename."</option>\r\n";
          GetSunOptionArray($row->ID,"─",$conn);
    }     
}
function GetSunOptionArray($ID,$step,$conn)
{
	$rs = mysql_query("Select * From dede_arttype where reID=".$ID,$conn);
	while($row=mysql_fetch_object($rs))
    {
         echo "<option value='".$row->ID."'>$step".$row->typename."</option>\r\n";
         GetSunOptionArray($row->ID,$step."─",$conn);
    }
}
?>