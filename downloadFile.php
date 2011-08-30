<?php
if(isset($_GET['conf']))
	$conf = $_GET['conf'];
else
	exit();

if($conf)
{
	$csvFile = "http://www.mooneygroup.org/stopOutput/" . $conf . ".csv";
	header('Content-Type: text/csv');
	header('Content-Disposition: attachment;filename="' . $csvFile . '"');
	$fp=fopen($csvFile,'r');
	fpassthru($fp);
	fclose($fp);
}

?>
