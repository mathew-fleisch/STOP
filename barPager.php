<?php
include '/var/www/inc/stop_config.php';
global $pageBy;
global $page;
global $pages;
$pages = 10;
$pageBy = 1000;
if(isset($_GET['page']))
{
	$page = $_GET['page'];
} else {
	$page = 1;
}


if($confID)
{
	$getRows = "select count(*)AS theCount from output where Confnum='$confID';";
	$rowsRes = mysql_query($getRows);
	if($rowsRes && mysql_num_rows($rowsRes))
	{
		echo "<b>Page: </b>";
		$count = mysql_fetch_assoc($rowsRes);
		$last = floor($count['theCount']/1000)+1;
		//echo $count['theCount'];
		$thisPage = curPageURL();
		$tempPage = preg_split("/\&page/", $thisPage);
		$thisPage = $tempPage[0];
		$i = 1;
		echo "
	<a href=\"$thisPage&page=1\">&lt;&lt;</a> | ";
		if($page > 1)
		{
			echo "
	<a href=\"$thisPage&page=" . ($page-1) . "\">prev</a> | ";
		}
		else
			echo "prev | ";
		while(($i*$pageBy) < ($count['theCount']+$pageBy))
		{
			//echo "i($i): page($page) - pages(" . ($page-5) . " " . ($page+5) . ")<br>";
			if($i > ($page-5) && $i < ($page+5))
			{	
				if($i != $page)
				{
					echo "
	<a href=\"$thisPage&page=$i\">$i</a> | ";
				}else{ echo $i . " | "; } 
			}
			elseif($page < 5 & $i < 10)
			{
				if($i != $page)
				{
					echo "
	<a href=\"$thisPage&page=$i\">$i</a> | ";
				}else{ echo $i . " | "; } 
			}
				$i++;


		}
		if($page < $last)
		{
			echo "
	<a href=\"$thisPage&page=" . ($page+1) . "\">next</a> | ";
		}
		else
			echo "next | ";
		echo "
	<a href=\"$thisPage&page=$last\">&gt;&gt;</a>";
	}
}

?>
