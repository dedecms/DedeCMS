<?php 
$spaceInfo = $dsql->GetOne("Select spacename,spaceimage,sex,c1,c2,spaceshow,logintime From #@__member where userid='$uid'; ");
?>