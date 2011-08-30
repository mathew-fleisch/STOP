<?php 
require_once 'config.php';
require_once '/var/www/inc/stop_config.php';
if($_GET['confID'])
{
	//$confNum = $_GET['confID'];
	echo "<input type=\"hidden\" id=\"conf_num\" value=\"" . $_GET['confID'] . "\">";
}
else
{
	echo "Must have confID...";
	exit();
}
?>

<div id="filter_container" style="display:none;">
	<div id="ontology_filter_container">
		<label for="ontology_filter">Showing Ontologies: </label>
		<span id='ontologies_filtered'>All</span>
		<a href='javascript:;' class='more'>Show</a>
		<label for="term_filter">Showing Terms:</label>
		<span id="terms_filtered">All</span>
		<label for="export_trigger">false</label>
		<span id="exporter"></span>
	</div>



	<div id="filter_dialog">
		<div id="filterSelector">
			<ul class="primary">
				<li class="active">
					<a href="javascript:;" id="ont_filter_btn">Ontology Filter</a>
				</li>
				<li class="inactive">
					<a href="javascript:;" id="term_filter_btn">Term Filter</a>
				</li>
			</ul>
		</div>
		<div id="term_container">
			<div class='filter_text_container'>
				Filter by text: <input id='term_filter_text' type="text" />
			</div>
			<div class='selection_container'>
				<a href='javascript:;' class='select_all_terms'>Select All</a>
				<a href='javascript:;' class='select_none_terms'>Select None</a>
			</div>

			<div class="list_container">
				<ul id='term_list'>

				</ul>			
			</div>
			<div class='termExclude_container'>
				Terms to Include: 
				<span class='selected_terms'>All</span>
				<a href='javascript:;' class='more'>Show</a>
			</div>
		</div>
		<div id="ont_container">
			<div id='menu_container'>
			<?php
			$getCategory = "select * from categories order by name;";
			$categoryRes = mysql_query($getCategory);
			if($categoryRes && mysql_num_rows($categoryRes))
			{
				echo "
				Filter by category:
				<br><select name=\"category\" id=\"category_menu\">
					<option value=\"all\">All Categories</option>";
				while($row = mysql_fetch_assoc($categoryRes))
				{
					echo "
					<option value=\"" . $row['id'] . "\">" . $row['name'] . "</option>";
				}
				echo "
				</select>";
			}
			?>
			</div>
			<div id='menu_container'>
			<?php
			$getGroup = "select * from groups order by name;";
			$groupRes = mysql_query($getGroup);
			if($groupRes && mysql_num_rows($groupRes))
			{
				echo "
				Filter by group:
				<br><select name=\"group\" id=\"group_menu\">
					<option value=\"all\">All Groups</option>";
				while($row = mysql_fetch_assoc($groupRes))
				{
					echo "
					<option value=\"" . $row['id'] . "\">" . $row['name'] . "</option>";
				}
				echo "
				</select>";
			}
	
			?>
			</div>
			<div class='filter_text_container'>
				Filter by text: <input id='ontology_filter_text' type="text" />
			</div>
			<div class='selection_container'>
				<a href='javascript:;' class='select_all'>Select All</a>
				<a href='javascript:;' class='select_none'>Select None</a>
			</div>
	
			<div class="list_container"><ul id='ontology_list'>
			<?php 
			$query = "select ont_id, name, groupIds, categoryIds from ontologies order by name asc";
			$stmt = $db->prepare($query);
			if ($stmt): 	
				$stmt->execute();
				$stmt->bind_result($ont_id, $name, $groups, $categories);
				while ($stmt->fetch()): ?>
					<li class='filter_item' data-ont_id='<?php echo $ont_id; ?>' data-ont_name='<?php echo $name; ?>' <?php 
					if($groups) { echo "data-groups='$groups' "; }else{ echo "data-groups='0' "; }
					if($categories) { echo "data-categories='$categories' "; }else{ echo "data-categories='0' "; }
					?>>
						<input class='filter_checkbox' type="checkbox" value="<?php echo $name; ?>" checked/><?php echo $name; ?>
					</li>
			<?php 
				endwhile;
			endif; ?>
			</ul></div>

			<div class='exclude_container'>
				Ontologies to Include: 
				<span class='selected_items'>All</span>
				<a href='javascript:;' class='more'>Show</a>
			</div>
		</div>
		<div class='button_container'>
			<button class='cancel'>Cancel</button>
			<button class='save'>Apply</button>
			<button class='export'>Export Filtered Results</button>
		</div>
	</div>
</div>
