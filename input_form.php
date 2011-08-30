<link rel="stylesheet" type="text/css" href="/stop/include/input_form.css"/>
<link rel="stylesheet" type="text/css" href="/stopOutput/ajax/css/ajax-tooltip.css"/>
<script type="text/javascript" src="/stop/include/jquery.js"></script>
<script type="text/javascript" src="/stop/include/input_form.js"></script>
<script type="text/javascript" src="/stopOutput/ajax/js/ajax-dynamic-content.js"></script>
<script type="text/javascript" src="/stopOutput/ajax/js/ajax.js"></script>
<script type="text/javascript" src="/stopOutput/ajax/js/ajax-tooltip.js"></script>
<p>
<form name="stop_form" method="post">
<?php if($_GLOBALS['user_email']){ echo "<input type=\"hidden\" name=\"user_email\" value=\"" . $_GLOBALS['user_email'] . "\">"; } ?>
<?php include 'aboutText.php'; ?>
<center>
<div id="formHolder">
	<table cellpadding="0" cellspacing="0" class="tableAtt">
		<tr>
			<td class="textboxwrapper" width="500">
				<a href="javascript:;" id="addLink">Add a title for this job</a>
				<a href="javascript:;" id="removeTitleLink" style="display:none;">Remove title</a>
				<br>
				<div id="addTitle" style="display:none; text-align:center;">
					<input type="text" name="stopTitle" id="textbox_thin"/>
				</div>
			</td>
		</tr>
		<tr>
			<td class="textboxwrapper1" width="500" style="padding: 0 10px 0 10px;">
				<table style="width:170px;">
					<tr>
						<td class="textboxwrapper1">
							Input: 
						</td>
						<td class="textboxwrapper1">
							<div id="geneWhat">
							<a href="javascript:;" onMouseOver="ajax_showTooltip(window.event,'/stopOutput/ajax/helpMsg.php?id=1', this);return false" onMouseOut="ajax_hideTooltip();return false" style="font-size:80%; color:#e10000; float:right;">what's this?</a>
							</div>
							<div id="protWhat" style="display:none;"><a href="javascript:;" onMouseOver="ajax_showTooltip(window.event,'/stopOutput/ajax/helpMsg.php?id=2', this);return false" onMouseOut="ajax_hideTooltip();return false" style="font-size:80%; color:#e10000; float:right;">what's this?</a></div>
						</td>
					</tr>
				</table>
				<p style="text-align:center">
				<textarea name="input" id="textbox"></textarea>
				</p>
			</td>
		</tr>
		<tr>
			<td class="textboxwrapper1" valign="top" width="500" style="padding: 0 10px 0 10px;">
				<a href="javascript:;" id="backLink">Define Custom Background</a>
				<a href="javascript:;" id="removeBack" style="display:none;">Remove Custom Background</a>
				<div id="background_set">
				Background:
				<p style="text-align:center">
				<textarea name="background" id="textbox1"></textarea>
				</p>
				</div>
			</td>
		</tr>
		<tr>
			<td>
			<table cellpadding="0" cellspacing="0">
				<tr>

					<td class="textboxwrapper" valign="top" style="padding-bottom:10px;">
						Select A Species:
						<br>

					<?php
						include '/var/www/inc/stop_config.php';
						$get = "select * from organisms where active = '1' order by ordered;";
						$res = mysql_query($get);
						if($res && mysql_num_rows($res))
						{

							echo "<select name=\"orgName\" style=\"width:120px;\" class=\"buttonBox\">";
							while($org = mysql_fetch_assoc($res))
							{
								$tempName = preg_split("/ /", $org['name']);
								$name = "";
								foreach($tempName as $n)
								{
									$name .= ucfirst($n) . " ";
								}
								echo "<option value=\"" . $org['name'] . "\">" . $name . "</option>";
							}
							echo "</select>";
						}
						else
						{
							echo "Error retrieving organism list...";
							exit();
						}
					?>
					</td>
					<td class="textboxwrapper" valign="top" style="padding-bottom:10px;">
						Multiple Hypthesis Correction
						<select name="multHyp" class="buttonBox">
							<option value="None">None</option>
							<option value="Benjamini-Hochberg" selected>Benjamini-Hochberg</option> 
							<option value="Bonferroni">Bonferroni</option> 
							<option value="Bonferroni-Holm">Bonferroni-Holm</option> 
						</select>
					</td>
					<?php /*
					<td class="ontologySelector">
						<div style="overflow:auto; height:150px; width:272px; padding-left:3px; border: 3px solid #ccc;">
						<?php
							include '/var/www/inc/stop_config.php';
							$get = "select * from ontologies;";
							$res = mysql_query($get);
							if($res && mysql_num_rows($res))
							{
	
								//echo "<select name=\"ontName\">";
								while($ont = mysql_fetch_assoc($res))
								{
									echo "<input type=\"checkbox\" name=\"ontology[]\" value=\"" . $ont['ont_id'] . "\">&nbsp;" . $ont['name'] . "<br>";
								}
								echo "</select>";
							}
							else
							{
								echo "Error retrieving ontology list...";
								exit();
							}
						?>

						</div>
						<input type="button" value="All" onClick="SetAllCheckBoxes('stop_form','ontology[]',true);">
						<input type="button" value="None" onClick="SetAllCheckBoxes('stop_form','ontology[]',false);">
					</td>
					*/ ?>
					<td class="textboxwrapper" valign="bottom" align="right" style="padding-bottom:10px;">
						<input type="submit" value="Submit" name="sumAnns" class="buttonBox" style="padding:5px;" onclick="$(this).hide();">
					</td>
				</tr>
				</table>
			</td>
		</tr>
	</table>
</center>
</div>
</form>
<script>SetAllCheckBoxes('stop_form','ontology[]',true);</script>
