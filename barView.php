<?php 
include '/var/www/inc/stop_config.php';

//dirty workaround while we can't make data.php publicly accessible to respond to AJAX requests
//this should really be in its own file
if (isset($_REQUEST['ajax'])) {
	if(isset($_REQUEST['exportMe']))
	{
		include 'exportData.php';
		exit;
	}
	else
	{
		include 'data.php';
		exit;
	}
}


$trig = false;
if(isset($_GET['confID']))
{
	$chk = "select id, conf_num from queue where conf_num = '" . strip_tags($_GET['confID']) . "';";
	$res = mysql_query($chk);
	if($res)
	{
		if(mysql_num_rows($res))
		{
			$trig = true;	
		}
	}
}

if($trig):
?>

<link rel="stylesheet" type="text/css" href="/stopOutput/ajax/css/ajax-tooltip.css"/>
<link rel="stylesheet" type="text/css" href="/stop/include/jquery.ui.css"/>
<link rel="stylesheet" type="text/css" href="/stop/include/filter.css"/>
<link rel="stylesheet" type="text/css" href="/stop/include/barView.css"/>

<ul class="primary">
	<li class="inactive"><a href="/stop/term-view?<?php echo $page; ?>&confID=<?php echo $_GET['confID']; ?>&email=<?php echo $_GET['email']; ?>">Term Cloud</a></li>
	<li class="active"><a href="/stop/bar-view?<?php echo $page; ?>&confID=<?php echo $_GET['confID']; ?>&email=<?php echo $_GET['email']; ?>">Bar Graph</a></li>
</ul>

<h2 style="padding-left:50px;">
<?php

/*
$getTitle = "select id,title,conf_num,timestamp from queue where conf_num='" . $_GET['confID'] . "';";
$titleRes = mysql_query($getTitle);
if($titleRes && mysql_num_rows($titleRes))
{
	$job = mysql_fetch_assoc($titleRes);
	if($job['title'])
		echo $job['title'] . " (" . $job['timestamp'] . ")";
	else
		echo $job['timestamp'];
}
*/
?>
</h2>

<div class="legendBox bar_view_pagination" style="height:17px; padding-top:2px;">
	<table>
		<tr>
			<td class='header'><strong>Page: </strong></td>
			<td class='first'>&lt;&lt;</td>
			<td class='last'>&gt;&gt;</td>
		</tr>
	</table>
</div>
<?php include 'legend.php'; ?>
<?php include 'filterView.php'; ?>

<table id="bar_view">
	<tr class="template">
		<td class="name_col" width="248">
			<a href="javascript:;"></a>
		</td>
		<td class="pval_col" width="323">
			<span></span>
		</td>
	</tr>
</table>
<div id="loading">
	<img src="/stop/include/loader.gif" alt="Loading..." />
</div>

<div class="legendBox bar_view_pagination" style="margin-top:10px;">
	<table>
		<tr>
			<td class='header'><strong>Page: </strong></td>
			<td class='first'>&lt;&lt;</td>
			<td class='last'>&gt;&gt;</td>
		</tr>
	</table>
</div>

<script type="text/javascript" src="/stopOutput/ajax/js/ajax-dynamic-content.js"></script>
<script type="text/javascript" src="/stopOutput/ajax/js/ajax.js"></script>
<script type="text/javascript" src="/stopOutput/ajax/js/ajax-tooltip.js"></script>
<?php //jquery ui depends on jquery so perhaps jquery should be here instead of in legend.php ?>
<script type="text/javascript" src="/stop/include/jquery.ui.js"></script>
<script type="text/javascript" src="/stop/include/filterView.js"></script>
<script type='text/javascript' src="/stop/include/barView.js"></script>
<?php endif; ?>
<?php if(!$trig): ?>
<h2>No results for this confID...</h2>
<?php endif; ?>
