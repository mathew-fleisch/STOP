<?php
require_once 'config.php';
header('Content-type: application/json');
header("HTTP/1.0 200", null, 200);
$return = new stdClass();
$return->error = true;

$pageBy = 1000;
$SQLorder = "correctedPVal, TermName";
$page = isset($_REQUEST['page']) && $_REQUEST['page'] ? intval(($_REQUEST['page']-1)) : 0;
$confID = isset($_REQUEST['confID']) ? $_REQUEST['confID'] : "5771938db304bba7923b0b6b05f761ac";

$notIn = '';
if (isset($_REQUEST['exclude']) && !empty($_REQUEST['exclude'])) {
	$exclude = explode(',', $_REQUEST['exclude']);
	//$notIn = "AND ont_id NOT IN(";//exclude checked
	$notIn = "AND ont_id IN(";//exclude unchecked
	$temp = array();
	foreach($exclude as $item) {
		array_push($temp, "'" . intval($item) . "'");
	}
	
	$notIn .= implode(",", $temp) . ")";
}

if (isset($_REQUEST['collapsed'])) {
	//$query = "select TermName,correctedPVal,Ench from output where Ench = 'ENR' and Confnum=? $notIn order by ? limit ?, ?;";
	//$countQuery = "select count(TermName) from output where Confnum=? $notIn;";
}
else {
	//$query = "select TermName,correctedPVal,Ench,count(TermName) AS `numGroup` from output where Ench = 'ENR' and Confnum=? $notIn group by TermName order by ? limit ?, ?;";
	$query = "select TermName,correctedPVal,Ench,count(TermName) AS `numGroup` from output where Ench = 'ENR' and Confnum=? $notIn group by TermName order by $SQLorder;";
	$countQuery = "select count(distinct TermName) from output where Confnum=? $notIn;";
}

$stmt = $db->prepare($query);
if (!$stmt) {
	$return->error_msg = 'Database error retrieving data';
	$return->query = $query;
	exit(json_encode($return));
}

$offset = $page * $pageBy;
$stmt->bind_param('s', $confID);
$stmt->execute();

if (isset($_GET['collapsed'])) {
	$stmt->bind_result($term, $correctedPval, $ench);
}
else {
	$stmt->bind_result($term, $correctedPval, $ench, $termCount);
}

$result = array();
$prevPval = 0;
$crntPval = 0;
while ($row = $stmt->fetch()) {

	//if($prevPval<=$correctedPval)
	//{
		//$correctedPval = $crntPval;
		//$crntPval++;
		$obj = new stdClass();
		$obj->term = $term;
		$obj->correctedPval = $correctedPval;
		$obj->stringPval = sprintf("%.2e", $correctedPval);
		//$obj->stringPval = $correctedPval . " " . $crntPval . " " . $prevPval . " " . ($prevPval<=$correctedPval);

		$t_Pval = ceil((log10($correctedPval)*-1)+8);
		if($t_Pval > 39)
			$b_Pval = 40;
		else
			$b_Pval = $t_Pval;
		$obj->fontSize = $b_Pval;
	
		$obj->ench = $ench;
		if (!isset($_GET['collapsed'])) {
			$obj->termCount = $termCount;
		}
		array_push($result, $obj);
//		$prevPval = $correctedPval;
//	}
	
}

$stmt = $db->prepare($countQuery);
if (!$stmt) {
	$return->error_msg = 'Database error retrieving count';
	$return->query = $countQuery;
	exit(json_encode($return));
}
$stmt->bind_param('s', $confID);
$stmt->execute();
$stmt->bind_result($count);
$stmt->fetch();

$return = new stdClass();
$return->count = $count;
$return->page = $page + 1;
$return->query = $query;
$return->countQuery = $countQuery;
$return->result = $result;

echo json_encode($return);

?>
