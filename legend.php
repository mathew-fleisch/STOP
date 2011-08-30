<script type="text/javascript" src="/stop/include/jquery.js"></script>
<script>

</script>
<script language="javascript" type="text/javascript">
$(document).ready(function(){

	$('#legend').click(function() {
		$('#inputText').toggle(700);
	});
	$('#aboutLink').click(function() {
		$('#aboutText').toggle(400);
	});

});
function SelectAll(id)
{
	    document.getElementById(id).focus();
	    document.getElementById(id).select();
}
</script>

<?php
include '/var/www/inc/stop_config.php';

$baseURL = "/content/bar-results";

if(isset($_GET['email']))
	$email = "email=".$_GET['email'];
else
	$email = "";


if(isset($_GET['confID']))
{
	$conf_id = "confID=".$_GET['confID'];
	$conf_num = $_GET['confID'];
}
else
	$conf_id = "";

if(isset($_GET['page']))
	$pageNum = "&page=".$_GET['page'];
else
	$pageNum = "";
	/*
echo "
<div class=\"legendBox\" id=\"filter\">
<span id=\"expand\" ";
	if(!$SQLcollapse)
		echo "style=\"display:none\"";
		echo ">
	<a href=\"$baseURL?$email&$conf_id" . $pageNum . "&collapse=1\">Expand</a>
</span>
<span id=\"collapse\" ";
	if($SQLcollapse)
		echo "style=\"display:none\"";
		echo ">
	<a href=\"$baseURL?$email&$conf_id" . $pageNum . "\">Collapse</a>
</span>";
		if(!$SQLcollapse)
			echo "<span id=\"ontology\"><a href=\"#\">";
		echo "Ontologies";

		if(!$SQLcollapse)
			echo "</a></span>";
		echo "
</div>
";
*/
$onts = array();
$ontTrack = 0;
$ontCount = 0;
$getOnts = "select ontology,conf_num from queue where conf_num = '$conf_num';";
$ontsRes = mysql_query($getOnts);
if($ontsRes && mysql_num_rows($ontsRes))
{
	$job = mysql_fetch_assoc($ontsRes);
	$ontologies = preg_split("/,/",$job['ontology']);
	$ontCount = count($ontologies);
	foreach($ontologies as $ontology)
	{
		$getName = "select * from ontologies where ont_id = '$ontology';";
		$nameRes = mysql_query($getName);
		if($nameRes && mysql_num_rows($nameRes))
		{
			$name = mysql_fetch_assoc($nameRes);
			$onts = array_push_assoc($onts, $ontology, $name['name']);
		}
	}
}
echo "
<div class=\"legendBox\" style=\"height:17px; padding-top:2px;\">
<a href=\"javascript:;\" id=\"legend\">Input Data</a>
</div>
<a href=\"javascript:;\" class=\"legendBox\" style=\"height: 17px; padding-top: 2px;\" id=\"ontology_filter_button\" onClick=\"ajax_hideTooltip();return false\">Filter Results</a>
<div class=\"legendBox\" style=\"height:17px; padding-top:2px; float:right;\">
<a href=\"javascript:;\" id=\"aboutLink\" style=\"padding:2px 5px 2px 5px;\">About STOP</a>
</div>
";

//echo "<a href=\"javascript:;\" id=\"aboutLink\" style=\"padding:2px 5px 2px 5px; border: 3px solid #ccc; font-size:86%; height:20px;\"><b>About STOP</b></a>";

echo "

<table style=\"height:1px; padding: 0; margin: 0; float:none;\"><td></td></table>";
include '/var/www/mooneygroup/stop/include/aboutText.php';
/*
<div id=\"ontologies\" style=\"display:none; border: 3px solid #ccc; padding: 0 5px 0 5px; margin: 5px;\">
<span id=\"closeOnt\"><a href=\"#\">
close</a></span><br>
	<table><td valign=\"top\">";
foreach($onts as $id=>$name)
{
	$ontTrack++;
	echo "
		<span class=\"ontToggle\" name=\"ontID_$id\">$name</span><br>";
	if($ontTrack)
	{
		if(ceil($ontCount/2) == $ontTrack)
		{
			echo "
	</td><td valign=\"top\">";
		}
	}

}
echo "
	</td></table>
</div>
";
*/
echo "

<table style=\"height:1px; padding: 0; margin: 0; float:none;\"><td></td></table>


<div id=\"inputText\" style=\"display:none; border: 3px solid #ccc; padding: 0 5px 0 5px; margin: 5px 5px 5px 5px;\">";
$getInput = "select input, organism, conf_num, protGene from queue where conf_num = '$conf_num';";
$inputRes = mysql_query($getInput);
if($inputRes)
{
	if(mysql_num_rows($inputRes))
	{
		$queue = mysql_fetch_assoc($inputRes);
		$org = "";
		$tOrg = preg_split("/\s/", $queue['organism']);
		foreach($tOrg as $t)
		{
			$org .= ucfirst($t) . " ";
		}
		$org = substr($org, 0, -1);
		$input = preg_replace("/\n/", ",", $queue['input']);
		$input = preg_replace("/\s/", "", $input);
		if($queue['protGene'] == "gene")
			$type = "Genes";
		else
			$type = "Proteins";
		echo "<h3>Input $type for $org:</h3><p style=\"line-height:150%;\">";
		$inputArray = preg_split("/,/", $input);
		$msg = displayNames($inputArray, $type);
		echo $msg . "</p>";
		echo "<table><tr><td style=\"width:120px;\"><b>Input $type Ids:</b></td><td><input type=\"text\" style=\"width:98%;\" name=\"inputData\" value=\"$input\" onClick=\"SelectAll('copy_id');\" id=\"copy_id\"></td></tr></table>";
	} else {
		echo "No result...";
	}
} else {
	echo "Mysql error... <p>" . mysql_error();
}



$getInput = "select notFound, conf_num, protGene from queue where conf_num = '$conf_num';";
$inputRes = mysql_query($getInput);
if($inputRes)
{
	if(mysql_num_rows($inputRes))
	{
		$queue = mysql_fetch_assoc($inputRes);
		if($queue['notFound'])
		{
			echo "
				<p style=\"height:15px;\"></p>";
			$notFound = preg_split("/,/", $queue['notFound']);
			if($queue['protGene'] == "gene")
				$type = "Genes";
			else
				$type = "Proteins";
			echo "<h3>These " . strtolower($type) . " were not found in our background set:</h3><p stype=\"line-height:150%;\">";
			$msg = displayNames($notFound, $type);
		echo $msg . "</p>";

		} else {
			//echo "No \"not found\"";

		}
	} else {
		echo "No result...";
	}
} else {
	echo "Mysql error... <p>" . mysql_error();
}
echo "
</div>
		";

function array_push_assoc($array, $key, $value){
	$array[$key] = $value;
	return $array;
}
function displayNames($inputArray, $type)
{
	$output = array();
	for($i = 0; $i < count($inputArray); $i++)
	{
		if($inputArray[$i])
		{
			$getName = "select * from gpNames where gpID = '" . $inputArray[$i] . "' and protGene = '" . strtolower(substr($type, 0, -1)) . "';";
			//echo $getName . "<br>";
			$nameRes = mysql_query($getName);
			if($nameRes)
			{
				if(mysql_num_rows($nameRes))
				{
					$name = mysql_fetch_assoc($nameRes);
					if($name['shortName'] == "error")
						$output = array_push_assoc($output, $inputArray[$i], $inputArray[$i]);
					elseif($name['shortName'] == "" && $name['longName'])
						$output = array_push_assoc($output, $inputArray[$i], $name['longName']);
					elseif($name['shortName'] == "" && $name['longName'] == "")
						$output = array_push_assoc($output, $inputArray[$i], $inputArray[$i]);
					else
						$output = array_push_assoc($output, $inputArray[$i], $name['shortName']);
				} else {
					//echo "No results...<br>";
					$output = array_push_assoc($output, $inputArray[$i], $inputArray[$i]);
				}
			} else {
			echo "Mysql error...<br>" . mysql_error();
			}
		}
	}
	$msg = "";
	foreach($output as $id=>$name)
	{
		if(is_numeric(substr($id, 0, 1)))
		{
			$msg .= "<a href=\"http://www.ncbi.nlm.nih.gov/gene/$id\" target=\"_blank\">$name</a>, ";
		} else {
			$msg .= "<a href=\"http://www.uniprot.org/uniprot/$id.html\" target=\"_blank\">$name</a>, ";
		}
	}
	$msg = substr($msg, 0, -2);
	return $msg;
}

function thisPage()
{
	$pageURL = 'http';
	if ($_SERVER["HTTPS"] == "on")
	{
		$pageURL .= "s";
	}
	$pageURL .= "://";
	if ($_SERVER["SERVER_PORT"] != "80")
	{
		$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
	} else {
		$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
	}
	return $pageURL;
}


?>
