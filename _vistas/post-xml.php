<?php
require_once '../_librerias/php/conexiones.php';
require_once '../_librerias/php/funciones.php';

$page = isset($_POST['page']) ? $_POST['page'] : 1;
$rp = isset($_POST['rp']) ? $_POST['rp'] : 10;
$sortname = isset($_POST['sortname']) ? $_POST['sortname'] : 'Nombres';
$sortorder = isset($_POST['sortorder']) ? $_POST['sortorder'] : 'desc';
$query = isset($_POST['query']) ? $_POST['query'] : false;
$qtype = isset($_POST['qtype']) ? $_POST['qtype'] : false;
$usingSQL = true;
// $page = $_POST['page'];
// $rp = $_POST['rp'];
$sortname = $_POST['sortname'];
$sortorder = $_POST['sortorder'];
if (!$sortname) $sortname = 'Nombres';
if (!$sortorder) $sortorder = 'desc';

$sort = " ORDER BY $sortname $sortorder";
// if (!$page) $page = 1;
// if (!$rp) $rp = 10;
$start = (($page-1) * $rp);
$limit = "LIMIT $start, $rp";
$where = "";
if ($query) $where = " WHERE $qtype LIKE '%".mysql_real_escape_string($query)."%' ";


///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

$sqlA = "SELECT CodAlumnos, Usuario, Nombres, ApellidosPat FROM alumnos $where $sort $limit";
$rows = fetchAll($sqlA);

$sql = "SELECT count(*) as regTot  FROM alumnos $where $sort $limit";
$rowsFO = fetch($sql);
$total = $rowsFO["regTot"];

header("Content-type: text/xml");
$xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
$xml .= "<rows>";
$xml .= "<page>$page</page>";
$xml .= "<total>$total</total>";
foreach($rows AS $row){
	$xml .= "<row id='".$row->CodAlumnos."'>";
	$xml .= "<cell><![CDATA[".utf8_encode($row->CodAlumnos)."]]></cell>";
	$xml .= "<cell><![CDATA[".utf8_encode($row->Usuario)."]]></cell>";
	$xml .= "<cell><![CDATA[".utf8_encode($row->Nombres)."]]></cell>";
	$xml .= "<cell><![CDATA[".utf8_encode($row->ApellidosPat)."]]></cell>";
	$xml .= "</row>";
}
$xml .= "</rows>";
echo $xml;