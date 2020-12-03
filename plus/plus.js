<!--
function SelectCatalog(fname,vname,bt,channelid,opall,pos){
   if($Nav()=='IE'){ var posLeft = window.event.clientX-100; var posTop = window.event.clientY; }
   else{ var posLeft = 100; var posTop = 100; }
   if(!fname) fname = 'form1';
   if(!vname) vname = 'typeid';
   if(!bt) vname = 'selct';
   window.open(pos+"/dialoguser/catalog_tree.php?f="+fname+"&opall="+opall+"&v="+vname+"&bt="+bt+"&channelid="+channelid, "popUpSelCWin", "scrollbars=yes,resizable=yes,statebar=no,width=450,height=300,left="+posLeft+", top="+posTop);
}
-->