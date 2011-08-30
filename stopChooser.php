<style>
.deleteBox {
	border: 1px solid #ccc;
	color: #000;
	background-color: #eee;
	margin-top:3px;
}
</style>
<?php
include '/var/www/inc/stop_config.php';

if(isset($_POST['changeName']))
{
	$conf	= $_POST['conf_num'];
	$time	= $_POST['timestamp'];
	$name	= addslashes(strip_tags($_POST['newName']));
	if($name != $time && $name)//Don't save timestamp as name... do nothing
	{
		$update = "update queue set title='$name' where conf_num='$conf';";
		$res = mysql_query($update);
		if(!$res)
			echo "There was a problem changing the name...";	
		else
			header('Location: ' . curPage());
	}
}

if(isset($_POST['deleteJob']))
{
	$conf = $_POST['conf_num'];
	$del_output = "delete from output where Confnum = '$conf';";
	$delOres = mysql_query($del_output);
	if($delOres)
	{
		$del_queue = "delete from queue where conf_num='$conf';";
		$delQres = mysql_query($del_queue);
		if($delQres)
		{
			header('Location: ' . curPage());
		}
		else
			echo "<script>alert('There was a problem while the file was being deleted from the queue...');</script>";
	}
	else
		echo "<script>alert('There was a problem while the file was being deleted from the output...');</script>";
}


if(isset($_POST['refreshMe']))
{
	$cmd = "/var/www/mooneygroup/stopOutput/startscripts.sh";
	$result = passthru($cmd);
//	echo "<p>Processing complete.<p>";
}
if($myUsername){

	global $myEmail;
	$get = "select * from queue where email='$myEmail' order by id desc;";
	$res = mysql_query($get);
	if($res)
	{
		if(mysql_num_rows($res))
		{
			$track = 0;
			echo "
<div class=\"rounded-block\">
	<div class=\"rounded-block-top-left\"></div>
	<div class=\"rounded-block-top-right\"></div>
	<div class=\"rounded-outside\">
	<div class=\"rounded-inside\">
	<p class=\"rounded-topspace\"></p>
	<h2 class=\"title block-title pngfix\">STOP Job Queue</h2>
	<div style=\"width:232px; background: url('/inc/topShadow.png') repeat-x; height:10px; position:absolute;\"></div>
	<div style=\"overflow:auto; height:200px;\">
	";

			$chkRes = mysql_query("select * from queue where email='$myEmail' and status < 3;");
			if($chkRes && mysql_num_rows($chkRes))
			{

			/*echo "
	<form method=\"post\">
		<input type=\"submit\" name=\"refreshMe\" value=\"Refresh Queue\">
	</form>";*/
			}
			$numRows = mysql_num_rows($res);
			echo "
		<ul style=\"text-align: left;\">";
			while($job = mysql_fetch_assoc($res))
			{
				$track++;
				$error = "";
				if($job['status'] == -1){
					$error = "<br>Input: <input type=\"text\" class=\"deleteBox\" style=\"background-color:#fff; color:#777; width:120px;\"
						value=\"" . preg_replace("/\n/", ",", $job['input']) . "\">";
					$status = "<br>Error: <span style=\"font-size:80%;\">" . preg_replace("/Start\ process/", "", $job['progress']) . "</span>";
				}elseif($job['status'] < 3){
					$status = "(Pending)";
					$refreshPage = "<a href=\"?action=refresh\">Rerun pending jobs...</a>";
				}else{
					$status = "";
					$refreshPage = "";
				}
				echo "
				<form method=\"post\">
					<input type=\"hidden\" name=\"conf_num\" value=\"" . $job['conf_num'] . "\">
					<li style=\"";
				if($track < $numRows)
					echo "border-bottom:2px solid #C7D0D8; padding: 0 0 8px 3px;";

				echo " margin: 0 3px 8px 3px; padding: 0 0 8px 3px;\">";
				if($job['title'])
				{
					echo "<b>" . $job['title'] . "</b> <a href=\"#\" onClick=\"show_" . $job['id'] . "()\" style=\"font-size:70%; margin: 0 0 5px 3px;\">[Rename]</a>
						<br style=\"font-size:70%\">" . $job['timestamp'] . "</br>";
					$jobName = addslashes(strip_tags($job['title']));
				}
				else
				{
					echo $job['timestamp'] . " <a href=\"#\" onClick=\"show_" . $job['id'] . "()\" style=\"font-size:70%; margin: 0 0 5px 3px;\">[Add Title]</a>";
					$jobName = $job['timestamp'];
				}
				
				echo "$error $status<div></div>
					<script>
						function show_" . $job['id'] . "() { 
							$('#edit_" . $job['id'] . "').toggle(500);
						}
					</script>
					<div id=\"edit_" . $job['id'] . "\" style=\"display:none;\">
					<input type=\"hidden\" name=\"conf_num\" value=\"" . $job['conf_num'] . "\">
					<input type=\"hidden\" name=\"timestamp\" value=\"" . $job['timestamp'] . "\">
					<table>
						<tr>
							<td>
							<input type=\"text\" name=\"newName\" value=\"";
					if($job['title'])
						echo preg_replace("/\"/", "\\\"", $job['title']);
					echo "\" style=\"background-color:#fff; width:120px;\" class=\"deleteBox\">
							</td>
							<td>
							<input type=\"submit\" name=\"changeName\" value=\"Change\" class=\"deleteBox\">
							</td>
						</tr>
					</table>
					</div>
			";
				if($job['status'] == 3)
				{
					echo "
	<input type=\"button\" class=\"deleteBox\" onClick=\"window.location.href='/stop/term-view?confID=" . $job['conf_num'] . "'\" value=\"Term Cloud\">
	<input type=\"button\" class=\"deleteBox\" onClick=\"window.location.href='/stop/bar-view?confID=" . $job['conf_num'] . "'\" value=\"Bar Graph\"><br>
	<input type=\"submit\" class=\"deleteBox\" onClick=\"window.open('/stop/include/downloadFile.php?conf=" . $job['conf_num'] . "');\" value=\"Download CSV\">";
				}

	echo "
		<input type=\"submit\" value=\"Delete\" class=\"deleteBox\" name=\"deleteJob\" onClick=\"return confirm('Are you sure you want to delete this job: \\n$jobName ";
	if($jobName != $job['timestamp'])
		echo "- (" . $job['timestamp'] . ")";
	echo "\\nConf: " . $job['conf_num'] . "');\">
";
	echo "</li>
		</form>
		";
				
			}
			echo "
		</ul>

	</div>
	<div style=\"width:100%; background: url('/inc/bottomShadow.png') repeat-x; height:10px; position:relative; margin-top: -8px;\"></div>
	<br>
	<a href=\"/logout\" style=\"padding-left:10px; text-decoration: none;\">logout</a>";
	if(!$myEmail)
		echo "<a href=\"/stop/input\" style=\"float:right; padding-right: 10px; text-decoration:none;\">STOP Input</a>";
	else
		echo "<a href=\"/stop/input?email=$myEmail\" style=\"float:right; padding-right:10px; text-decoration:none;\">STOP Input</a>";
	echo "	
	<p class=\"rounded-bottomspace\"></p>
	</div>
	</div>
	<div class=\"rounded-block-bottom-left\"></div>
	<div class=\"rounded-block-bottom-right\"></div>
</div>
";
		}
		else
		{
			echo "
<div class=\"rounded-block\">
	<div class=\"rounded-block-top-left\"></div>
	<div class=\"rounded-block-top-right\"></div>
	<div class=\"rounded-outside\">
	<div class=\"rounded-inside\">
	<p class=\"rounded-topspace\"></p>	
	<h2 class=\"title block-title pngfix\">STOP Dashboard</h2>
	<div style=\"text-align: center; padding: 5px 5px 5px 5px;\">
	You have not submitted anything yet.
	<br>
	<a href=\"/stop/input\">Click Here</a> to input your dataset to STOP.
	</div>
	<p class=\"rounded-bottomspace\"></p>
	</div>
	</div>
	<div class=\"rounded-block-bottom-left\"></div>
	<div class=\"rounded-block-bottom-right\"></div>
</div>";

		}
	}
	else
	{
		echo "Error " . mysql_error();
	}
}
elseif(!$myUsername && $myEmail && isset($_GET['confID'])){
	
	include '/var/www/inc/stop_config.php';
	$confID = strip_tags($_GET['confID']);
	global $myEmail;
	$get = "select * from queue where conf_num = '$confID' order by id desc;";
	$res = mysql_query($get);
	if($res)
	{
		if(mysql_num_rows($res))
		{
			$track = 0;
			echo "
<div class=\"rounded-block\">
	<div class=\"rounded-block-top-left\"></div>
	<div class=\"rounded-block-top-right\"></div>
	<div class=\"rounded-outside\">
	<div class=\"rounded-inside\">
	<p class=\"rounded-topspace\"></p>
	<h2 class=\"title block-title pngfix\">STOP Job Queue</h2>
	<div style=\"width:232px; background: url('/inc/topShadow.png') repeat-x; height:10px; position:absolute;\"></div>
	<div style=\"overflow:auto; height:80px;\">



	<ul style=\"text-align: left;\">";
	

			$chkRes = mysql_query("select * from queue where email='$myEmail' and status < 3;");
			if($chkRes && mysql_num_rows($chkRes))
			{
			/*
			echo "
	<form method=\"post\">
		<input type=\"submit\" name=\"refreshMe\" value=\"Refresh Queue\">
	</form>";
			*/
			}
			$numRows = mysql_num_rows($res);

			while($job = mysql_fetch_assoc($res))
			{
				$track++;
				if($job['status'] == -1){
					$status = "<br>Error: " . ucfirst($job['progress']);
				}elseif($job['status'] < 3){
					$status = "(Pending)";
					$refreshPage = "<a href=\"?action=refresh\">Rerun pending jobs...</a>";
				}else{
					$status = "";
					$refreshPage = "";
				}
				echo "
				<form method=\"post\">
					<li style=\"";
				if($track < $numRows)
					echo "border-bottom:2px solid #C7D0D8; padding: 0 0 8px 3px;";

				echo " margin: 0 3px 0 3px;\">";
				if($job['title'])
				{
					echo "<b>" . $job['title'] . "</b> <a href=\"#\" onClick=\"show_" . $job['id'] . "()\" style=\"font-size:70%; margin: 0 0 5px 3px;\">[Rename]</a>
						<br style=\"font-size:70%\">" . $job['timestamp'] . "</br>";
					$jobName = addslashes(strip_tags($job['title']));
				}
				else
				{
					echo $job['timestamp'] . " <a href=\"#\" onClick=\"show_" . $job['id'] . "()\" style=\"font-size:70%; margin: 0 0 5px 3px;\">[Add Title]</a>";

					$jobName = $job['timestamp'];
				}
				
				echo " $status<div></div>
					<script>
						function show_" . $job['id'] . "() { 
							$('#edit_" . $job['id'] . "').toggle(500);
						}
					</script>
					<div id=\"edit_" . $job['id'] . "\" style=\"display:none;\">
					<input type=\"hidden\" name=\"conf_num\" value=\"" . $job['conf_num'] . "\">
					<input type=\"hidden\" name=\"timestamp\" value=\"" . $job['timestamp'] . "\">
					<table>
						<tr>
							<td>
							<input type=\"text\" name=\"newName\" value=\"";
					
					if($job['title'])
						echo preg_replace("/\"/", "'", $job['title']);

					echo "\" style=\"background-color:#fff; width:120px;\" class=\"deleteBox\">
							</td>
							<td>
							<input type=\"submit\" name=\"changeName\" value=\"Change\" class=\"deleteBox\">
							</td>
						</tr>
					</table>
					</div>
			";
				if($job['status'] == 3)
				{
					echo "
	<input type=\"button\" class=\"deleteBox\" onClick=\"window.location.href='/stop/term-view?email=$myEmail&confID=" . $job['conf_num'] . "'\" value=\"Term Cloud\">
	<input type=\"button\" class=\"deleteBox\" onClick=\"window.location.href='/stop/bar-view?email=$myEmail&confID=" . $job['conf_num'] . "'\" value=\"Bar Graph\"><br>
	<input type=\"submit\" class=\"deleteBox\" onClick=\"window.open('/stop/include/downloadFile.php?conf=" . $job['conf_num'] . "');\" value=\"Download CSV\">";
				}
	echo "
		<input type=\"submit\" class=\"deleteBox\" value=\"Delete\" name=\"deleteJob\" onClick=\"return confirm('Are you sure you want to delete this job: \\n$jobName ";
	if($jobName != $job['timestamp'])
		echo "- (" . $job['timestamp'] . ")";
	echo "\\nConf: " . $job['conf_num'] . "');\">
";
	echo "</li>
		</form>
		";
				
			}
			echo "
		</ul>


	</div>
	<div style=\"width:100%; background: url('/inc/bottomShadow.png') repeat-x; height:10px; position:relative; margin-top: -8px;\"></div>
	<br>
	<a href=\"/stop/input\" style=\"padding-left:10px; text-decoration: none;\">logout</a>";
	if(!$myEmail)
		echo "<a href=\"/stop/input\" style=\"float:right; padding-right: 10px; text-decoration:none;\">STOP Input</a>";
	else
		echo "<a href=\"/stop/input?email=$myEmail\" style=\"float:right; padding-right:10px; text-decoration:none;\">STOP Input</a>";
	echo "	
	<p class=\"rounded-bottomspace\"></p>
	</div>
	</div>
	<div class=\"rounded-block-bottom-left\"></div>
	<div class=\"rounded-block-bottom-right\"></div>
</div>
";
		}
		else
		{
			echo "
<div class=\"rounded-block\">
	<div class=\"rounded-block-top-left\"></div>
	<div class=\"rounded-block-top-right\"></div>
	<div class=\"rounded-outside\">
	<div class=\"rounded-inside\">
	<p class=\"rounded-topspace\"></p>	
	<h2 class=\"title block-title pngfix\">STOP Dashboard</h2>
	<div style=\"padding:5px;\">
	There is no data for these parameters...
	<br>
	<a href=\"/stop/input\" style=\"padding-left:10px; text-decoration: none;\">logout</a>";
	if(!$myEmail)
		echo "<a href=\"/stop/input\" style=\"float:right; padding-right: 10px; text-decoration:none;\">STOP Input</a>";
	else
		echo "<a href=\"/stop/input?email=$myEmail\" style=\"float:right; padding-right:10px; text-decoration:none;\">STOP Input</a>";
	echo "</div>
	<p class=\"rounded-bottomspace\"></p>
	</div>
	</div>
	<div class=\"rounded-block-bottom-left\"></div>
	<div class=\"rounded-block-bottom-right\"></div>
</div>";

		}
	}
	else
	{
		echo "Error " . mysql_error();
	}
}
else
{
	echo "
<div class=\"rounded-block\">
	<div class=\"rounded-block-top-left\"></div>
	<div class=\"rounded-block-top-right\"></div>
	<div class=\"rounded-outside\">
	<div class=\"rounded-inside\">
	<p class=\"rounded-topspace\"></p>
	<h2 class=\"title block-title pngfix\">STOP Dashboard</h2>
	<div style=\"text-align:center; padding: 5px;\">		
	";
	if(!$confID)
	{
		if(isset($_POST['viewSingle']) && isset($_POST['new_conf']))
		{
			header('Location: http://mooneygroup.org/stop/bar-view?email='.$myEmail.'&confID='.$_POST['new_conf']);
		}
		echo "Must have valid confirmation ID to view STOP Job Queue";
		echo "<hr>
<form method=\"post\">
<table>
	<tr>
		<td valign=\"top\" height=\"20\">
			<input type=\"text\" name=\"new_conf\" value=\"Enter Confirmation ID\" onFocus=\"$(this).val(''); $(this).css('color','#000');\" style=\"color:#aaa; border: 2px solid #ccc; width:150px;\">
		</td>
		<td valign=\"top\" height=\"20\">
			<input type=\"submit\" name=\"viewSingle\" value=\"View\" class=\"deleteBox\" style=\"margin-top:0;\">
		</td>
	</tr>
	<tr>
		<td colspan=\"2\" align=\"left\">
			<a href=\"/stop/input\" style=\"font-size:80%; font-weight:bold;\">logout</a>
		</td>
	</tr>
</table>
</form>

			";
	}
	if(!$myEmail)
	{
		echo "Must be logged in (at least email) to use STOP
		<span style=\"font-size:80%; font-weight:bold; float:right; padding: 3px 0 0 0;\">
		<a href=\"/stop/input\">logout</a>
		</span>
			";
	}
	echo "
		

	</div>
	<p class=\"rounded-bottomspace\"></p>
	</div>
	</div>
	<div class=\"rounded-block-bottom-left\"></div>
	<div class=\"rounded-block-bottom-right\"></div>
</div>";
}



	
	
	
	
	

?>
