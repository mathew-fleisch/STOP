<?php 
//dirty workaround while we can't make data.php publicly accessible to respond to AJAX requests
//this should really be in its own file
if (isset($_REQUEST['ajax'])) {
	include 'tagData.php';
	exit;
}
?>

<link rel="stylesheet" type="text/css" href="/stopOutput/ajax/css/ajax-tooltip.css"/>
<link rel="stylesheet" type="text/css" href="/stop/include/jquery.ui.css"/>
<link rel="stylesheet" type="text/css" href="/stop/include/filter.css"/>
<link rel="stylesheet" type="text/css" href="/stop/include/stopView.css" />

<ul class="primary">
	<li class="active"><a href="/stop/term-view?<?php echo $page; ?>&confID=<?php echo $_GET['confID']; ?>&email=<?php echo $_GET['email']; ?>">Term Cloud</a></li>
	<li class="inactive"><a href="/stop/bar-view?<?php echo $page; ?>&confID=<?php echo $_GET['confID']; ?>&email=<?php echo $_GET['email']; ?>">Bar Graph</a></li>
</ul>
<?php
include '/var/www/inc/stop_config.php';
/*
if(isset($_GET['order']))
{
	$order = $_GET['order'];
	switch($order)
	{
		case "TermName":
		case "TermID":
		case "StudyCount":
		case "StudyTotal":
		case "BGCount":
		case "BGTotal":
		case "Ench":
		case "uncorrectedPVal":
		case "correctedPVal":
			$SQLorder = $order;
		break;
		default:
			$SQLorder = "correctedPVal, TermName";
			break;
			
	}
}
else
	$SQLorder = "correctedPVal, TermName";


if(isset($_GET['where']))
{
	$where = $_GET['where'];
	$activeWhere = 0;
	switch($where)
	{
		case "filter":
			$SQLwhere = "and correctedPVal < 0.05";
			$activeWhere = 1;
		break;
		default:
			$SQLwhere = "";
	}
}
else
	$SQLwhere = "";

//$temp = "0c62cf55621df35bae573a5dd853fc44";
$temp = 0;
	
if(isset($_GET['confID']) || $temp )
{
	if(isset($_GET['confID']))
		$confID = $_GET['confID'];
	else
		$confID = $temp;

	$CrntPage = curPageURL();
	$page = preg_replace('/^.*\?/','',$CrntPage);
echo "


<p>

";
}

*/



function curPageURL() 
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
function commify ($str) { 
	$n = strlen($str); 
	if ($n <= 3) { 
		$return=$str;
	} 
	else { 
		$pre=substr($str,0,$n-3); 
		$post=substr($str,$n-3,3); 
		$pre=commify($pre); 
		$return="$pre,$post"; 
	}
	return($return); 
}

?>
<?php include 'legend.php'; ?>
<?php include 'filterView.php'; ?>
<div id="stop_view">
	<span class="template">
		<span class="name_value">
			<a href="javascript:;"></a>
		</span>
	</span>
</div>
<div id="loading">
	<img src="/stop/include/loader.gif" alt="Loading..." />
</div>

<script type="text/javascript" src="/stopOutput/ajax/js/ajax-dynamic-content.js"></script>
<script type="text/javascript" src="/stopOutput/ajax/js/ajax.js"></script>
<script type="text/javascript" src="/stopOutput/ajax/js/ajax-tooltip.js"></script>
<script type="text/javascript" src="/stop/include/jquery.ui.js"></script>
<script type="text/javascript" src="/stop/include/filterView.js"></script>
<script type="text/javascript" src="/stop/include/stopView.js"></script>
