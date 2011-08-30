<?php 
require_once 'config.php';

if($_POST)
{
	$search = addslashes(strip_tags($_POST['searchString']));
	$confNum = $_POST['conf'];
}
else
{
	echo "error...";
	exit();
}

$rows = 0;
$query = "select Confnum, id, TermName,  ont_name, correctedPVal from output where Confnum = '$confNum' and TermName like '%$search%' order by TermName, correctedPVal;";
$stmt = $db->prepare($query);
if ($stmt): 	
	$last = "";
	$stmt->execute();
	$stmt->store_result();
	$num_rows = $stmt->num_rows;
	if($num_rows > 3000)
	{
		echo "<span id=\"errorMsg\">To many results...</span>";
		exit();
	}
	else
	{
		$stmt->bind_result($tempConf, $termID, $termName, $ont_name, $pval);
		while ($stmt->fetch()): 
			$rows++;
			//if($last != strtolower($termName)): ?>
			<li class='filter_item_term' data-term_id='<?php echo $termID; ?>' data-term_name='<?php echo $termName; ?>'>

			<input class='filter_checkbox_terms' type="checkbox" value="<?php echo $termName; ?>" checked/>
			<span style="font-size:80%; color:#33DD00;"><?php echo sprintf("%.2e", $pval); ?></span>
			<span style="font-size:75%;"><?php //echo substr($ont_name, 0, 9); ?></span>
			<?php echo $termName; ?>
		</li>
		<?php 
			$last = strtolower($termName);
		endwhile;
	}
endif; 
if(!$rows)
{
	echo "
<span id=\"errorMsg\">No results for \"$search\"</span>
<input type=\"hidden\" id=\"errorTrig\" value=\"1\">";
}
else
{
	echo "<input type=\"hidden\" id=\"errorTrig\" value=\"0\">";
}
?>
