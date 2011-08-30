<?php
require_once 'config.php';
header('Content-type: application/json');
header("HTTP/1.0 200", null, 200);
$return = new stdClass();
$return->error = true;

//$pageBy = 1000;
$SQLorder = "correctedPVal, TermName";
//$page = isset($_REQUEST['page']) && $_REQUEST['page'] ? intval(($_REQUEST['page']-1)) : 0;
$confID = isset($_REQUEST['confID']) ? $_REQUEST['confID'] : "5771938db304bba7923b0b6b05f761ac";
$termIn = '';
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

if(isset($_REQUEST['termExclude']) && !empty($_REQUEST['termExclude']))
{
	$termExclude = explode(",", $_REQUEST['termExclude']);
	$termIn = "AND id IN(";
	$temp = array();
	foreach($termExclude as $item)
	{
		array_push($temp, "'" . intval($item) . "'");
	}
	$termIn .= implode(",", $temp) . ")";
}

//$query = "select *,count(TermName) AS `numGroup` from output where Ench = 'ENR' and Confnum=? $notIn $termIn group by TermName order by $SQLorder;";
$query = "select ont_id,ont_name,TermName,TermID,StudyCount,StudyTotal,BGCount,BGTotal,UncorrectedPVal,correctedPVal,Ench,count(TermName) AS `numGroup` from output where Ench = 'ENR' and Confnum=? $notIn $termIn group by TermName order by $SQLorder;";
$countQuery = "select count(distinct TermName) from output where Confnum=? $notIn $termIn;";

$stmt = $db->prepare($query);
if (!$stmt) {
	$return->error_msg = 'Database error retrieving data';
	$return->query = $query;
	exit(json_encode($return));
}

//$offset = $page * $pageBy;
//$stmt->bind_param('sii', $confID, $offset, $pageBy);
$stmt->bind_param('s', $confID);
$stmt->execute();

if (isset($_GET['collapsed'])) {
	$stmt->bind_result($term, $correctedPval, $ench);
}
else {
//	$stmt->bind_result($id, $timestamp, $conf, $ont_id, $ont_name, $term_id, $term, $study_count, $study_total, $bg_count, $bg_total, $study_genes, $bg_genes, $uncorrectedPval, $ench, $correctedPval, $termCount);
	$stmt->bind_result($ont_id, $ont_name, $term, $term_id, $study_count, $study_total, $bg_count, $bg_total, $uncorrectedPval, $correctedPval, $ench, $termCount);
}

$result = array();
$prevPval = 0;
$crntPval = 0;
//echo "id,Ontology ID,Ontology,Term ID,Term Name,Study Count,Study Total,Background Count,Background Total,Study Genes,Background Genes,Uncorrected P-Value,Corrected P-Value";
while ($row = $stmt->fetch()) {
	//echo "$id,$ont_id,$ont_name,$term_id,$term,$study_count,$study_total,$bg_count,$bg_total,$study_genes,$bg_genes,$uncorrectedPval,$correctedPval,$termCount\n";
	
		$obj = new stdClass();
		//$obj->id		= $id;
		$obj->ont_id		= $ont_id;
		$obj->ont_name		= $ont_name;
		$obj->term_id		= $term_id;
		$obj->term		= $term;
		$obj->study_count	= $study_count;
		$obj->study_total	= $study_total;
		$obj->bg_count		= $bg_count;
		$obj->bg_total		= $bg_total;
		//$obj->study_genes	= $study_genes;
		//$obj->bg_genes		= $bg_genes;
		$obj->uncorrectedPval	= $uncorrectedPval;
		$obj->correctedPval	= $correctedPval;
		$obj->ench = $ench;
		//$obj->stringPval = sprintf("%.2e", $correctedPval);

		/*$t_Pval = (log10($correctedPval)*-1);
		if($t_Pval > 49)
			$b_Pval = 323;
		else
			$b_Pval = round($t_Pval * 6.46);
		$obj->width = $b_Pval / 3.23;
	

		*/
		if (!isset($_GET['collapsed'])) {
			$obj->termCount = $termCount;
		}
		array_push($result, $obj);
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
//$return->page = $page + 1;
$return->query = $query;
$return->countQuery = $countQuery;
$return->result = $result;
echo json_encode($return);

?>
