<?php
header("Content-type: application/vnd.ms-excel; name='excel'");
header("Content-Disposition: filename=STOP_Export.csv");
header("Pragma: no-cache");
header("Expires: 0");

$result = $_POST['exportdata'];
//print_r($result);
echo $result;

?>
