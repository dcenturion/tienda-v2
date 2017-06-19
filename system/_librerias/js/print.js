function PrintElem(elem)
{
	//Popup($('<div/>').append($(elem).clone()).html());
	Popup($(elem).html());
}

function Popup(data) 
{
	var mywindow = window.open('', 'Print', '');
	mywindow.document.write('<html><head>');
	//mywindow.document.write('<link rel="stylesheet" href="/system/_estilos/Style_Print.css" type="text/css" media="print" />');
	mywindow.document.write('<style>' +
								'.botones1 li{display: none;}' +
								'.TituloA h1 { font-size: 1.5em; line-height: 5px; }' +
								'.TituloA span { font-size: 0.6em; color: #839191; line-height: 13px;font-weight: normal; }' +
								'.TituloA p { font-size: 0.7em; line-height: 18px;font-weight: 700; }' +
								'.TituloA .linea { border-top: 1px solid #E6E3E3;width: 100%;height: 1px;margin: 0;margin-top: -10px; }' +
								'.IndicadorA { position: relative; width: 100%; }' +
								'.IndicadorA .IA-N-Numero {font-size: 4em;font-weight: 300;}' +
								'.IndicadorA .IA-N-Texto { position: absolute; left: 170px; top: 82px;font-size: 0.7em; color: #A6A4A4; }' +
								'.IndicadorA .IA-N-TextoB { position: absolute; left: 170px; top: 95px;color: #0FA1C3; font-size: 1.8em; }' +
								'.cuadrosA02 { margin: 0.1em 0.1em 0.3em 0.1em; padding: 6px;position: relative; overflow: auto; font-size: 0.8em; float: left;border: 1px solid #F0F3F2; }' +
								'.cuadrosA02 .body_cuadrosA { font-size: 0.7em; color: #009688; }' +
								'th { border: 1px solid #ddd;font-size: 16px;}' +
								'td { border: 1px solid #ddd;color: #8f8f8f;font-size: 12px;padding: 10px 10px 10px 10px;text-align: center;}' +
								'table {border-collapse: collapse;}' +
								'body {border: 1px solid #cccccc;padding: 20px 30px 20px 30px;font-family: Arial,Helvetica,sans-serif,"Open Sans";}' +
							'</style>');
	mywindow.document.write('</head><body>');
	mywindow.document.write(data);
	mywindow.document.write('</body></html>');

	mywindow.document.close(); // necessary for IE >= 10
	mywindow.focus(); // necessary for IE >= 10

	mywindow.print();
	mywindow.close();

	return true;
}

function imprimir(id)
{
	var div, imp;
	div = document.getElementById(id);
	imp = window.open(" ","Formato de Impresion"); 
	imp.document.open();    
	imp.document.write("<style>#Hoja { text-align: center;font-family: arial; }#DivImg { margin: 2em 0;height: 100px;position: relative; }#Img { width: 260px;height: 100px;position: absolute;right: 2em; }#Tipo { font-weight: 600;text-align: center;margin: 0 auto; }#Name { font-family:Lucida Calligraphy;font-size: 2em;margin: 1.5em 7em;text-align: center; }#Text { margin: 3em 7em 6em 7em;text-align: center;}#Raya { width: 250px;margin: 0 auto; }#Firma { text-align: center;margin: 0.5em 7em;}</style>");
	imp.document.write('<img src="../../ArchivosEmpresa/marco.png" style="width: 93%;height: 90%;position: absolute;top: 0;left: 0;z-index: 0;opacity: 0.5;border: 1.7em #6BC9F5 solid;">'+div.innerHTML);
	imp.document.close();
	imp.print();   
	imp.close(); 
}

							
/*
<style type="text/css" media="print,screen">

@media print 
 {<style type="text/css" media="print">@page port {size: portrait;}@page land {size: landscape;}.portrait {page: port;}.landscape {page: land;}</style>
*/











