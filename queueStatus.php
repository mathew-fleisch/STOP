<script>
function calcHeight()
{
	var the_height=10;
	document.getElementById('glu').height=the_height;
	
	the_height=
		document.getElementById('glu').contentWindow.
		document.body.scrollHeight;
	document.getElementById('glu').height=the_height;
}

</script>
<style>
a:link, a:visited {
	text-decoration:none; 
	color:#33839A;
}
a:hover {
	text-decoration:none;
	color: #144A6E;
}
</style>
<div style="font-family: Tahoma, Verdana, Arial, Helvetica; line-height:150%; color: #555; width:600px;">
<?php
@apache_setenv('no-gzip',1);
@ini_set('zlib.output_conpresion',0);
@ini_set('implicit_flush',1);
for($i=0;$i<ob_get_level(); $i++) { ob_end_flush(); }
ob_implicit_flush(1);
include '/var/www/inc/stop_config.php';

if(isset($_GET['confID']))
{
$confID = $_GET['confID'];
}
else
{
echo "Must have Confirmation ID";
exit();
}



flush_buffers();
$trigger = true;
$errTrig = false;
$status = null;
$prev = $status;
$track = 0;
$start = microtime(true);
$dotTrack = 0;
while($trigger)
{
	$chkStatus = "select * from queue where conf_num = '$confID'";
	//echo $chkStatus . "<p>";
	$statusRes = mysql_query($chkStatus);
	if($statusRes && mysql_num_rows($statusRes))
	{
		$queue = mysql_fetch_assoc($statusRes);
		$stat = preg_split("/\s/",$queue['progress']);
		$status = "";
	//	$status = $queue['progress'];
		foreach($stat as $s)
		{
			$status .= ucfirst($s) . " ";
		}
		$status = substr($status, 0, -1);

		if($queue['title'])
			$title = $queue['title'];
		else
			$title = $queue['timestamp'];
		if(!$track)
			echo "Processing started for $title<p style=\"margin:0;\" id=\"crazy\">";

		if($status != $prev && trim($status))
		{
			echo "<script language=\"JavaScript\">document.getElementById('crazy').innerHTML=''</script>";
			echo "<br>" . trim($status);
		//	echo trim($status);
			$dotTrack = 0;
		}
		if($status=="")
		{
			if($dotTrack > 1 && $dotTrack < 10)
				echo ".";
			elseif(!($dotTrack % 10))
				echo ".";
			$dotTrack++;
		}
		$prev = $status;
	}
	$track++;
	if(trim($status) == "done" || $queue['status'] == 3)
	{
		$end = microtime(true);
		$time = ceil($end-$start);
		echo " ($time seconds to process)<br>
			<a href=\"/stop/bar-view?confID=$confID\" target=\"_parent\">
			Click Here to view results</a>";
		$trigger = false;
	}
	elseif($queue['status'] == -1)
	{
		echo "<br>Please be patient while STOP redirects you to the input page...";
		$trigger = false;
		$errTrig = true;
	}
	if($track > 1000)
		$trigger = false;
	sleep(1);
	flush_buffers();
}



//echo "</p>";

function flush_buffers() 
{
ob_end_flush();
ob_flush();
flush();
ob_start();
}

if($errTrig):
	sleep(5);
?>

<script>
window.parent.location.href = "http://www.mooneygroup.org/stop/";
</script>

<?php endif; ?>

</div>
