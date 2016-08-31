<html>
<HEAD>
<TITLE>Color Palette1</TITLE>
<SCRIPT LANGUAGE='JavaScript'>
<!--
function colorcode1(fieldname)
{
	document.all.fillcode.innerHTML="<table border=0 cellpadding=0 cellspacing=0><tr><td bgcolor="+document.forms[0].colorcode.value+"><a href=\"javascript:fillcolor('"+document.forms[0].colorcode.value+"','"+fieldname+"')\"><img src='../images/spacer.gif' width='20' height='20' border='0'></a></TD></tr></table>";
}

function fillcolor(fillvalue,fieldname1)
{
<?php //esse array guarda as cores de legenda dos alertas, cores que constarem aqui, n�o poder�o ser selecionadas
	if (isset($_GET['jausadas']) && !empty($_GET['jausadas'])) {
		echo "var jausados = '".$_GET['jausadas']."';\n";
		echo "var RE = new RegExp(',?'+fillvalue.substr(1)+',?');\n";
		echo "if (jausados.match(RE)){ alert('Essa cor j� est� em uso por outro t�pico'); return ;}\n";		 
	}
?>	
	if(fillvalue=="document.forms[0].colorcode.value")
		fillvalue="";
	abc="window.opener.document.form1.<?php echo $_GET['field'] ?>.value"+"=fillvalue";
	eval(abc);
	changefocus="window.opener.document.form1.<?php echo $_GET['field'] ?>.focus()";
	eval(changefocus);
	window.close();
}
//-->
</SCRIPT>
</HEAD>
<BODY leftmargin=0 topmargin=0>
<TABLE border='1' cellpadding='0' cellspacing='0'>
<TR>
	<TD width='15' height="13" bgcolor='#FFFF00'><a href="javascript:fillcolor('#ffff00','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#FFFF33'><a href="javascript:fillcolor('#ffff33','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#FFFF66'><a href="javascript:fillcolor('#ffff66','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#FFFF99'><a href="javascript:fillcolor('#ffff99','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#FFFFCC'><a href="javascript:fillcolor('#ffffcc','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#FFFFFF'><a href="javascript:fillcolor('#ffffff','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#CCFFFF'><a href="javascript:fillcolor('#ccffff','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#CCFFCC'><a href="javascript:fillcolor('#ccffcc','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#CCFF99'><a href="javascript:fillcolor('#ccff99','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#CCFF66'><a href="javascript:fillcolor('#ccff66','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#CCFF33'><a href="javascript:fillcolor('#ccff33','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#CCFF00'><a href="javascript:fillcolor('#ccff00','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
</TR>
<TR>
	<TD width='15' height="13" bgcolor='#FFCC00'><a href="javascript:fillcolor('#ffcc00','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#FFCC33'><a href="javascript:fillcolor('#ffcc33','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#FFCC66'><a href="javascript:fillcolor('#ffcc66','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#FFCC99'><a href="javascript:fillcolor('#ffcc99','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#FFCCCC'><a href="javascript:fillcolor('#ffcccc','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#FFCCFF'><a href="javascript:fillcolor('#ffccff','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#CCCCFF'><a href="javascript:fillcolor('#ccccff','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#CCCCCC'><a href="javascript:fillcolor('#cccccc','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#CCCC99'><a href="javascript:fillcolor('#cccc99','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#CCCC66'><a href="javascript:fillcolor('#cccc66','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#CCCC33'><a href="javascript:fillcolor('#cccc33','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#CCCC00'><a href="javascript:fillcolor('#cccc00','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
</TR>
<TR>
	<TD width='15' height="13" bgcolor='#FF9900'><a href="javascript:fillcolor('#ff9900','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#FF9933'><a href="javascript:fillcolor('#ff9933','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#FF9966'><a href="javascript:fillcolor('#ff9966','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#FF9999'><a href="javascript:fillcolor('#ff9999','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#FF99CC'><a href="javascript:fillcolor('#ff99cc','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#FF99FF'><a href="javascript:fillcolor('#ff99ff','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#CC99FF'><a href="javascript:fillcolor('#cc99ff','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#CC99CC'><a href="javascript:fillcolor('#cc99cc','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#CC9999'><a href="javascript:fillcolor('#cc9999','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#CC9966'><a href="javascript:fillcolor('#cc9966','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#CC9933'><a href="javascript:fillcolor('#cc9933','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#CC9900'><a href="javascript:fillcolor('#cc9900','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
</TR>
<TR>
	<TD width='15' height="13" bgcolor='#FF6600'><a href="javascript:fillcolor('#ff6600','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#FF6633'><a href="javascript:fillcolor('#ff6633','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#FF6666'><a href="javascript:fillcolor('#ff6666','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#FF6699'><a href="javascript:fillcolor('#ff6699','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#FF66CC'><a href="javascript:fillcolor('#ff66cc','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#FF66FF'><a href="javascript:fillcolor('#ff66ff','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#CC66FF'><a href="javascript:fillcolor('#cc66ff','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#CC66CC'><a href="javascript:fillcolor('#cc66cc','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#CC6699'><a href="javascript:fillcolor('#cc6699','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#CC6666'><a href="javascript:fillcolor('#cc6666','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#CC6633'><a href="javascript:fillcolor('#cc6633','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#CC6600'><a href="javascript:fillcolor('#cc6600','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
</TR>
<TR>
	<TD width='15' height="13" bgcolor='#FF3300'><a href="javascript:fillcolor('#ff3300','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#FF3333'><a href="javascript:fillcolor('#ff3333','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#FF3366'><a href="javascript:fillcolor('#ff3366','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#FF3399'><a href="javascript:fillcolor('#ff3399','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#FF33CC'><a href="javascript:fillcolor('#ff33cc','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#FF33FF'><a href="javascript:fillcolor('#ff33ff','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#CC33FF'><a href="javascript:fillcolor('#cc33ff','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#CC33CC'><a href="javascript:fillcolor('#cc33cc','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#CC3399'><a href="javascript:fillcolor('#cc3399','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#CC3366'><a href="javascript:fillcolor('#cc3366','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#CC3333'><a href="javascript:fillcolor('#cc3333','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#CC3300'><a href="javascript:fillcolor('#cc3300','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
</TR>
<TR>
	<TD width='15' height="13" bgcolor='#FF0000'><a href="javascript:fillcolor('#ff0000','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#FF0033'><a href="javascript:fillcolor('#ff0033','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#FF0066'><a href="javascript:fillcolor('#ff0066','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#FF0099'><a href="javascript:fillcolor('#ff0099','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#FF00CC'><a href="javascript:fillcolor('#ff00cc','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#FF00FF'><a href="javascript:fillcolor('#ff00ff','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#CC00FF'><a href="javascript:fillcolor('#cc00ff','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#CC00CC'><a href="javascript:fillcolor('#cc00cc','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#CC0099'><a href="javascript:fillcolor('#cc0099','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#CC0066'><a href="javascript:fillcolor('#cc0066','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#CC0033'><a href="javascript:fillcolor('#cc0033','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#CC0000'><a href="javascript:fillcolor('#cc0000','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
</TR>
<TR>
	<TD width='15' height="13" bgcolor='#660000'><a href="javascript:fillcolor('#660000','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#660033'><a href="javascript:fillcolor('#660033','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#660066'><a href="javascript:fillcolor('#660066','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#660099'><a href="javascript:fillcolor('#660099','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#6600CC'><a href="javascript:fillcolor('#6600cc','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#6600FF'><a href="javascript:fillcolor('#6600ff','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#9900FF'><a href="javascript:fillcolor('#9900ff','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#9900CC'><a href="javascript:fillcolor('#9900cc','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#990099'><a href="javascript:fillcolor('#990099','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#990066'><a href="javascript:fillcolor('#990066','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#990033'><a href="javascript:fillcolor('#990033','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#990000'><a href="javascript:fillcolor('#990000','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
</TR>
<TR>
	<TD width='15' height="13" bgcolor='#663300'><a href="javascript:fillcolor('#663300','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#663333'><a href="javascript:fillcolor('#663333','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#663366'><a href="javascript:fillcolor('#663366','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#663399'><a href="javascript:fillcolor('#663399','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#6633CC'><a href="javascript:fillcolor('#6633cc','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#6633FF'><a href="javascript:fillcolor('#6633ff','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#9933FF'><a href="javascript:fillcolor('#9933ff','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#9933CC'><a href="javascript:fillcolor('#9933cc','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#993399'><a href="javascript:fillcolor('#993399','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#993366'><a href="javascript:fillcolor('#993366','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#993333'><a href="javascript:fillcolor('#993333','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#993300'><a href="javascript:fillcolor('#993300','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
</TR>
<TR>
	<TD width='15' height="13" bgcolor='#666600'><a href="javascript:fillcolor('#666600','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#666633'><a href="javascript:fillcolor('#666633','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#666666'><a href="javascript:fillcolor('#666666','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#666699'><a href="javascript:fillcolor('#666699','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#6666CC'><a href="javascript:fillcolor('#6666cc','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#6666FF'><a href="javascript:fillcolor('#6666ff','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#9966FF'><a href="javascript:fillcolor('#9966ff','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#9966CC'><a href="javascript:fillcolor('#9966ff','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#996699'><a href="javascript:fillcolor('#996699','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#996666'><a href="javascript:fillcolor('#996666','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#996633'><a href="javascript:fillcolor('#996633','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#996600'><a href="javascript:fillcolor('#996600','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
</TR>
<TR>
	<TD width='15' height="13" bgcolor='#669900'><a href="javascript:fillcolor('#669900','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#669933'><a href="javascript:fillcolor('#669933','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#669966'><a href="javascript:fillcolor('#669966','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#669999'><a href="javascript:fillcolor('#669999','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#6699CC'><a href="javascript:fillcolor('#6699cc','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#6699FF'><a href="javascript:fillcolor('#6699ff','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#9999FF'><a href="javascript:fillcolor('#9999ff','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#9999CC'><a href="javascript:fillcolor('#9999cc','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#999999'><a href="javascript:fillcolor('#999999','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#999966'><a href="javascript:fillcolor('#999966','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#999933'><a href="javascript:fillcolor('#999933','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#999900'><a href="javascript:fillcolor('#999900','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
</TR>
<TR>
	<TD width='15' height="13" bgcolor='#66CC00'><a href="javascript:fillcolor('#66cc00','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#66CC33'><a href="javascript:fillcolor('#66cc33','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#66CC66'><a href="javascript:fillcolor('#66cc66','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#66CC99'><a href="javascript:fillcolor('#66cc99','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#66CCCC'><a href="javascript:fillcolor('#66cccc','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#66CCFF'><a href="javascript:fillcolor('#66ccff','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#99CCFF'><a href="javascript:fillcolor('#99ccff','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#99CCCC'><a href="javascript:fillcolor('#99cccc','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#99CC99'><a href="javascript:fillcolor('#99cc99','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#99CC66'><a href="javascript:fillcolor('#99cc66','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#99CC33'><a href="javascript:fillcolor('#99cc33','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#99CC00'><a href="javascript:fillcolor('#99cc00','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
</TR>
<TR>
	<TD width='15' height="13" bgcolor='#66FF00'><a href="javascript:fillcolor('#66ff00','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#66FF33'><a href="javascript:fillcolor('#66ff33','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#66FF66'><a href="javascript:fillcolor('#66ff66','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#66FF99'><a href="javascript:fillcolor('#66ff99','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#66FFCC'><a href="javascript:fillcolor('#66ffcc','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#66FFFF'><a href="javascript:fillcolor('#66ffff','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#99FFFF'><a href="javascript:fillcolor('#99ffff','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#99FFCC'><a href="javascript:fillcolor('#99ffcc','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#99FF99'><a href="javascript:fillcolor('#99ff99','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#99FF66'><a href="javascript:fillcolor('#99ff66','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#99FF33'><a href="javascript:fillcolor('#99ff33','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#99FF00'><a href="javascript:fillcolor('#99ff00','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
</TR>
<TR>
	<TD width='15' height="13" bgcolor='#33FF00'><a href="javascript:fillcolor('#33ff00','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#33FF33'><a href="javascript:fillcolor('#33ff33','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#33FF66'><a href="javascript:fillcolor('#33ff66','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#33FF99'><a href="javascript:fillcolor('#33ff99','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#33FFCC'><a href="javascript:fillcolor('#33ffcc','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#33FFFF'><a href="javascript:fillcolor('#33ffff','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#00FFFF'><a href="javascript:fillcolor('#00ffff','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#00FFCC'><a href="javascript:fillcolor('#00ffcc','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#00FF99'><a href="javascript:fillcolor('#00ff99','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#00FF66'><a href="javascript:fillcolor('#00ff66','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#00FF33'><a href="javascript:fillcolor('#00ff33','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#00FF00'><a href="javascript:fillcolor('#00ff00','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
</TR>
<TR>
	<TD width='15' height="13" bgcolor='#33CC00'><a href="javascript:fillcolor('#33cc00','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#33CC33'><a href="javascript:fillcolor('#33cc33','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#33CC66'><a href="javascript:fillcolor('#33cc66','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#33CC99'><a href="javascript:fillcolor('#33cc99','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#33CCCC'><a href="javascript:fillcolor('#33cccc','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#33CCFF'><a href="javascript:fillcolor('#33ccff','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#00CCFF'><a href="javascript:fillcolor('#00ccff','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#00CCCC'><a href="javascript:fillcolor('#00cccc','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#00CC99'><a href="javascript:fillcolor('#00cc99','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#00CC66'><a href="javascript:fillcolor('#00cc66','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#00CC33'><a href="javascript:fillcolor('#00cc33','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#00CC00'><a href="javascript:fillcolor('#00cc00','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
</TR>
<TR>
	<TD width='15' height="13" bgcolor='#339900'><a href="javascript:fillcolor('#339900','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#339933'><a href="javascript:fillcolor('#339933','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#339966'><a href="javascript:fillcolor('#339966','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#339999'><a href="javascript:fillcolor('#339999','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#3399CC'><a href="javascript:fillcolor('#3399cc','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#3399FF'><a href="javascript:fillcolor('#3399ff','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#0099FF'><a href="javascript:fillcolor('#0099ff','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#0099CC'><a href="javascript:fillcolor('#0099cc','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#009999'><a href="javascript:fillcolor('#009999','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#009966'><a href="javascript:fillcolor('#009966','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#009933'><a href="javascript:fillcolor('#009933','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#009900'><a href="javascript:fillcolor('#009900','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
</TR>
<TR>
	<TD width='15' height="13" bgcolor='#336600'><a href="javascript:fillcolor('#336600','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#336633'><a href="javascript:fillcolor('#336633','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#336666'><a href="javascript:fillcolor('#336666','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#336699'><a href="javascript:fillcolor('#336699','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#3366CC'><a href="javascript:fillcolor('#3366cc','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#3366FF'><a href="javascript:fillcolor('#3366ff','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#0066FF'><a href="javascript:fillcolor('#0066ff','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#0066CC'><a href="javascript:fillcolor('#0066cc','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#006699'><a href="javascript:fillcolor('#006699','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#006666'><a href="javascript:fillcolor('#006666','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#006633'><a href="javascript:fillcolor('#006633','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#006600'><a href="javascript:fillcolor('#006600','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
</TR>
<TR>
	<TD width='15' height="13" bgcolor='#333300'><a href="javascript:fillcolor('#333300','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#333333'><a href="javascript:fillcolor('#333333','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#333366'><a href="javascript:fillcolor('#333366','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#333399'><a href="javascript:fillcolor('#333399','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#3333CC'><a href="javascript:fillcolor('#3333cc','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#3333FF'><a href="javascript:fillcolor('#3333ff','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#0033FF'><a href="javascript:fillcolor('#0033ff','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#0033CC'><a href="javascript:fillcolor('#0033cc','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#003399'><a href="javascript:fillcolor('#003399','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#003366'><a href="javascript:fillcolor('#003366','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#003333'><a href="javascript:fillcolor('#003333','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#003300'><a href="javascript:fillcolor('#003300','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
</TR>
<TR>
	<TD width='15' height="13" bgcolor='#330000'><a href="javascript:fillcolor('#330000','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#330033'><a href="javascript:fillcolor('#330033','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#330066'><a href="javascript:fillcolor('#330066','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#330099'><a href="javascript:fillcolor('#330099','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#3300CC'><a href="javascript:fillcolor('#3300cc','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#3300FF'><a href="javascript:fillcolor('#3300ff','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#0000FF'><a href="javascript:fillcolor('#0000ff','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#0000CC'><a href="javascript:fillcolor('#0000cc','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#000099'><a href="javascript:fillcolor('#000099','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#000066'><a href="javascript:fillcolor('#000066','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#000033'><a href="javascript:fillcolor('#000033','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
	<TD width='15' height="13" bgcolor='#000000'><a href="javascript:fillcolor('#000000','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
</TR>
<TR>
  <TD height="13" bgcolor='#000000'><a href="javascript:fillcolor('#000000','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
  <TD height="13" bgcolor='#333333'><a href="javascript:fillcolor('#333333','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
  <TD height="13" bgcolor='#666666'><a href="javascript:fillcolor('#666666','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
  <TD height="13" bgcolor='#999999'><a href="javascript:fillcolor('#999999','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
  <TD height="13" bgcolor='#CCCCCC'><a href="javascript:fillcolor('#CCCCCC','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
  <TD height="13" bgcolor='#CCCCCC'><a href="javascript:fillcolor('#CCCCCC','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
  <TD height="13" bgcolor='#CCCCCC'><a href="javascript:fillcolor('#CCCCCC','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
  <TD height="13" bgcolor='#CCCCCC'><a href="javascript:fillcolor('#CCCCCC','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
  <TD height="13" bgcolor='#D4D4D4'><a href="javascript:fillcolor('#D4D4D4','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
  <TD height="13" bgcolor='#DDDDDD'><a href="javascript:fillcolor('#DDDDDD','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
  <TD height="13" bgcolor='#EBEBEB'><a href="javascript:fillcolor('#EBEBEB','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
  <TD height="13" bgcolor='#FFFFFF'><a href="javascript:fillcolor('#FFFFFF','Color')"><img src='../images/spacer.gif' width='15' height='14' border='0'></a></TD>
</TR>
<TR>
	<TD colspan='12' valign='top' bgcolor='#B6B6B6'>

		<TABLE>
			<TR>
				<form>
				<TD><font face='ms sans serif' size='-2' color='#000000'>Digite a Cor:</font></TD>
				<TD><INPUT TYPE='text' NAME='colorcode' size='10' onKeyUp="javascript:colorcode1('Color')" maxlength=10></TD>
				<TD>
					<table border='1' cellpadding='0' cellspacing='0'>
						<tr>
							<td><div id='fillcode' style='background-color:#FFFFFF; layer-background-color:#FFFFFF; visibility:visible;'><a href="javascript:fillcolor('document.forms[0].colorcode.value','Color')"><img src='../images/spacer.gif' width='20' height='20' border='0'></a></div></td>
						</tr>
					</table>
				</TD>
				</form>
			</TR>
		</TABLE>
	</TD>
</TR>
</TABLE>

</BODY>
</HTML>
