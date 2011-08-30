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
	$stmt->bind_result($tempConf, $termID, $termName, $ont_name, $pval);
	echo "
<table cellpadding=\"0\" cellspacing=\"0\">
";
/*
echo "
	<tr>
		<td></td>
		<td><span style=\"font-size:75%;\">P-Value</span></td>
		<td><span style=\"font-size:80%;\">Ontology</span></td>
		<td>Term Name</td>
	</tr>";
	*/
	while ($stmt->fetch()): 
		$rows++;
		//if($last != strtolower($termName)): ?>
		<tr>
		<td>
			<li class='filter_item_term' data-term_id='<?php echo $termID; ?>' data-term_name='<?php echo $termName; ?>'>

			<input class='filter_checkbox_terms' type="checkbox" value="<?php echo $termName; ?>" checked/>
		</td>
		<td>
			<span style="font-size:80%; color:#33DD00;"><?php echo sprintf("%.2e", $pval); ?></span>
		</td>
		<td>
			<span style="font-size:75%;"><?php //echo substr($ont_name, 0, 9); ?></span>
		</td>
		<td>
			<?php echo $termName; ?>
		</li>
		</td>
		</tr>
<?php 
		//endif;
		$last = strtolower($termName);
	endwhile;
		echo "</table>";
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
