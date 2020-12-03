<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML><HEAD><TITLE>颜色</TITLE>
<META content="text/html; charset=gb2312" http-equiv=Content-Type>
<STYLE type=text/css>TD {
FONT-SIZE: 10.8pt
}
BODY {
FONT-SIZE: 10.8pt
}
BUTTON {
WIDTH: 5em
}
</STYLE>

<SCRIPT language=JavaScript>
var SelRGB = '';
var DrRGB = '';
var SelGRAY = '120';

var hexch = new Array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'A', 'B', 'C', 'D', 'E', 'F');

function ToHex(n)
{	var h, l;

n = Math.round(n);
l = n % 16;
h = Math.floor((n / 16)) % 16;
return (hexch[h] + hexch[l]);
}

function DoColor(c, l)
{ var r, g, b;

r = '0x' + c.substring(1, 3);
g = '0x' + c.substring(3, 5);
b = '0x' + c.substring(5, 7);

if(l > 120)
{
l = l - 120;

r = (r * (120 - l) + 255 * l) / 120;
g = (g * (120 - l) + 255 * l) / 120;
b = (b * (120 - l) + 255 * l) / 120;
}else
{
r = (r * l) / 120;
g = (g * l) / 120;
b = (b * l) / 120;
}

return '#' + ToHex(r) + ToHex(g) + ToHex(b);
}

function EndColor()
{ var i;

if(DrRGB != SelRGB)
{
DrRGB = SelRGB;
for(i = 0; i <= 30; i ++)
GrayTable.rows(i).bgColor = DoColor(SelRGB, 240 - i * 8);
}

SelColor.value = DoColor(RGB.innerText, GRAY.innerText);
ShowColor.bgColor = SelColor.value;
}
</SCRIPT>

<SCRIPT event=onclick for=ColorTable language=JavaScript>
SelRGB = event.srcElement.bgColor;
EndColor();
</SCRIPT>

<SCRIPT event=onmouseover for=ColorTable language=JavaScript>
RGB.innerText = event.srcElement.bgColor;
EndColor();
</SCRIPT>

<SCRIPT event=onmouseout for=ColorTable language=JavaScript>
RGB.innerText = SelRGB;
EndColor();
</SCRIPT>

<SCRIPT event=onclick for=GrayTable language=JavaScript>
SelGRAY = event.srcElement.title;
EndColor();
</SCRIPT>

<SCRIPT event=onmouseover for=GrayTable language=JavaScript>
GRAY.innerText = event.srcElement.title;
EndColor();
</SCRIPT>

<SCRIPT event=onmouseout for=GrayTable language=JavaScript>
GRAY.innerText = SelGRAY;
EndColor();
</SCRIPT>

<SCRIPT event=onclick for=Ok language=JavaScript>
window.returnValue = SelColor.value;
window.close();
</SCRIPT>

<META content="MSHTML 5.00.2920.0" name=GENERATOR></HEAD>
<BODY bgColor=menu>
<DIV align=center>
<CENTER>
<TABLE border=0 cellPadding=0 cellSpacing=10>
<TBODY>
<TR>
<TD>
<TABLE border=0 cellPadding=0 cellSpacing=0 id=ColorTable
style="CURSOR: hand">
<SCRIPT language=JavaScript>
function wc(r, g, b, n)
{
r = ((r * 16 + r) * 3 * (15 - n) + 0x80 * n) / 15;
g = ((g * 16 + g) * 3 * (15 - n) + 0x80 * n) / 15;
b = ((b * 16 + b) * 3 * (15 - n) + 0x80 * n) / 15;

document.write('<TD BGCOLOR=#' + ToHex(r) + ToHex(g) + ToHex(b) + ' height=8 width=8></TD>');
}

var cnum = new Array(1, 0, 0, 1, 1, 0, 0, 1, 0, 0, 1, 1, 0, 0, 1, 1, 0, 1, 1, 0, 0);

for(i = 0; i < 16; i ++)
{
document.write('<TR>');
for(j = 0; j < 30; j ++)
{
n1 = j % 5;
n2 = Math.floor(j / 5) * 3;
n3 = n2 + 3;

wc((cnum[n3] * n1 + cnum[n2] * (5 - n1)),
(cnum[n3 + 1] * n1 + cnum[n2 + 1] * (5 - n1)),
(cnum[n3 + 2] * n1 + cnum[n2 + 2] * (5 - n1)), i);
}

document.writeln('</TR>');
}
</SCRIPT>

<TBODY></TBODY></TABLE></TD>
<TD>
<TABLE border=0 cellPadding=0 cellSpacing=0 id=GrayTable
style="CURSOR: hand">
<SCRIPT language=JavaScript>
for(i = 255; i >= 0; i -= 8.5)
document.write('<TR BGCOLOR=#' + ToHex(i) + ToHex(i) + ToHex(i) + '><TD TITLE=' + Math.floor(i * 16 / 17) + ' height=4 width=20></TD></TR>');
</SCRIPT>

<TBODY></TBODY></TABLE></TD></TR></TBODY></TABLE></CENTER></DIV>
<DIV align=center>
<CENTER>
<TABLE border=0 cellPadding=0 cellSpacing=10>
<TBODY>
<TR>
<TD align=middle rowSpan=2>选中色彩
<TABLE border=1 cellPadding=0 cellSpacing=0 height=30 id=ShowColor
width=40>
<TBODY>
<TR>
<TD></TD></TR></TBODY></TABLE></TD>
<TD rowSpan=2>基色: <SPAN id=RGB></SPAN><BR>亮度: <SPAN
id=GRAY>120</SPAN><BR>代码: <INPUT id=SelColor size=7></TD>
<TD><BUTTON id=Ok type=submit>确定</BUTTON></TD></TR>
<TR>
<TD><BUTTON
onclick=window.close();>取消</BUTTON></TD></TR></TBODY></TABLE></CENTER></DIV></BODY></HTML>