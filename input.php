<?php
//error_reporting(E_ALL);
//if (is_resource(STDOUT)) fclose(STDOUT);
//if (is_resource(STDERR)) fclose(STDERR); 

$aboutLink = " <a href=\"#\" id=\"aboutLink\" style=\"margin-left:20px; padding: 2px 5px 2px 5px; border: 3px solid #ccc; font-size:80%;\">About <b>STOP</b></a>";
if($myUsername){
	echo "<div style=\"margin-top:10px;\">Welcome " . $myUsername . $aboutLink . "</div>
		<div style=\"width:510px; margin: 0 auto;\">";
	include 'input_form.php';
}
elseif(isset($_GET['email']))
{
	echo "<div style=\"margin-top:10px\">Welcome " . $_GET['email'] . $aboutLink . "</div>
		<div style=\"width:510px; margin: 0 auto;\">";
	include 'input_form.php';
}
else
{
	include 'aboutText.php';
echo "<script>
$(document).ready(function() {
	$('#aboutText').css('display', 'block');	
});
</script>
";
}
if(isset($_POST['sumAnns']))
{
	if(!(isset($_POST['input'])) || ($_POST['input'] == ""))
	{
		echo "<span style=\"padding:5px;\">Must input at least one identifier...</span>";
	}
	else
	{
		if(isset($_POST['user_email']))
		{
			$myEmail = $_POST['user_email'];
			echo $myEmail;
		}
		if(isset($_GET['email']))
			$myEmail = $_GET['email'];
	
		if($_POST['stopTitle'])
			$title 	= "'" . addslashes(strip_tags($_POST['stopTitle'])) . "'";
		else
			$title 	= 'null'; 
	
	
		$input		= strip_tags($_POST['input']);
		$background	= strip_tags($_POST['background']);
		$organism	= $_POST['orgName'];
		//$ontology	= implode(",",$_POST['ontology']);	
		$token		= md5(uniqid(rand(), true));
		$timestamp	= date('M d, Y - g:i:s A');
		$org		= $_POST['orgName'];
		//$protGene	= $_POST['protGene'];
	        $delimit	= $_POST['delimit'];
		
		$multHyp	= $_POST['multHyp'];
		
//		if($delimit == 1){
//			$del = ",";
//		}
//		if($delimit == 2){
//			$del = "\n";
//		}
//		if($delimit == 3){
//			$del = "\t";
//		}
//		if($delimit == 4){
//			$del = " ";
//		}
//		if($delimit == 5){
//			$del = "-";
//		}
//		$tempDel = explode($del, $input);
//		if(is_numeric(substr($tempDel[0], 0, 1)))
//			$protGene = "gene";
//		else
//			$protGene = "protein";
	
//		$input = implode("\n",$tempDel);
	
		echo "
<script>
$(document).ready(function() {
	$('#formHolder').toggle(300);
});
</script>
";
		$put = "insert into queue (input, background, title, MultipleHypothesisCorrection, organism, timestamp, status,  email, conf_num)
			VALUES ('$input', '$background', $title, '$multHyp', '$organism', '$timestamp', 1, '$myEmail', '$token');";
		mysql_query($put) or die('Error entering info into db...<br>'. $put . '<p>' . mysql_error());
		//echo $put;
	
		//$cmd = 'nohup /var/www/mooneygroup/stopOutput/startscripts.sh 2>&1 > /dev/null &';
		putenv("PYTHON_EGG_CACHE=/export/shared_apps/python-2.6/egg-cache");
		putenv("PATH=/sbin:/usr/sbin:/bin:/usr/bin:/usr/local/bin");
	//	$cmd = 'nohup python /var/www/mooneygroup/stop/startAnalysisfromWeb.py > /dev/null 2>&1 &';
		$cmd = "mlaunch sh stop.sh $token";
		$ps = shell_exec($cmd);
//		$cmd2 = 'echo $token > /var/www/mooneygroup/stop/dummytest.txt'
//		$ps2 = shell_exec($cmd2);
		echo "Submission Successful<br>";
		echo "<iframe src=\"/stop/include/queueStatus.php?confID=$token\" frameborder=\"0\" 
			onLoad=\"calcHeight();\" scrolling=\"no\" id=\"glu\" target=\"_self\"
			width=\"100%\" style=\"padding:10px;\"></iframe>";
	}
}

function alert($msg){
	echo "<script>alert('$msg');</script>";
}



function run_in_background($Command, $Priority = 0)
{
	if($Priority)
		$PID = shell_exec("nohup nice -n $Priority $Command 2> /dev/null & echo $!");
	else
		$PID = shell_exec("nohup $Command 2> \/dev\/null \& echo \$\!");
	return($PID);
}

function is_process_running($PID)
{
	exec("ps $PID", $ProcessState);
	return(count($ProcessState)>= 2);
}
?>
</div>
