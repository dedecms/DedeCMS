var dedeqrcodeLink = document.getElementById('__dedeqrcode_'+__dedeqrcode_id);
dedeqrcodeLink.style.display = 'none';
var randNum = Math.floor(Math.random() * 2147483648).toString(36);
var __dedeqrcode_src = "\""+__dedeqrcode_dir+"/qrcode.php?id="+__dedeqrcode_aid+"&type="+__dedeqrcode_type+"\"";


document.writeln("<ins style=\"display:inline-table;border:none;margin:0;padding:0;position:relative;visibility:visible;width:100%\">");
document.writeln("  <ins id=\"__bfzInc_"+randNum+"\" style=\"display:block;border:none;margin:0;padding:0;position:relative;visibility:visible;width:100%\">");
document.writeln("<iframe id=\"dede_qrcode_frame\" name=\"dede_qrcode_frame\" width=\"260\" height=\"280\" frameborder=\"0\" src="+__dedeqrcode_src+" marginwidth=\"0\" marginheight=\"0\" vspace=\"0\" hspace=\"0\" allowtransparency=\"true\" scrolling=\"no\" allowfullscreen=\"true\"></iframe>");
document.writeln("  </ins>");
document.writeln("</ins>");