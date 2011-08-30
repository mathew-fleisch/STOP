$(document).ready(function() {
	var filterTimer = null;
	var names = [];
	var ids = [];
	var term_names = [];
	var term_ids = [];
	var showState = false;
	var showState2 = false;
	var limit = 5;
	var outerShowState = false;
	var outerLimit = 20;
	var filterChangeDisabled = false;
	var filterTermsChangeDisabled = false;
	
	$("#filter_dialog").dialog({ 
		autoOpen: false,
		title: "Filter",
		width: "600px"
	}).show();
	
	$("#ontology_filter_button").click(function() {
		$("#filter_dialog").dialog("open");
	});

	$("#ont_filter_btn").click(function() {
		$("#ont_container").show();
		$("#term_container").hide();
		$("#term_filter_btn").parent().removeClass("active");
		$("#term_filter_btn").parent().addClass("inactive");
		$("#ont_filter_btn").parent().removeClass("inactive");
		$("#ont_filter_btn").parent().addClass("active");

	});

	$("#term_filter_btn").click(function() {
		$("#ont_container").hide();
		$("#term_container").show();
		$("#term_filter_btn").parent().removeClass("inactive");
		$("#term_filter_btn").parent().addClass("active");
		$("#ont_filter_btn").parent().removeClass("active");
		$("#ont_filter_btn").parent().addClass("inactive");

	});

	$("#ontology_filter_container .more").click(function() {
		outerShowState = !outerShowState;
		updateExcludeString(names, outerLimit, outerShowState, $("#ontologies_filtered"));
	});

	//Instant search for Ontology filter.
	$("#ontology_filter_text").keyup(function(event) {
		clearTimeout(filterTimer);
		filterTimer = setTimeout(function() {
			var text = $("#ontology_filter_text").val().toLowerCase();
			$('#ontology_list > li.filter_item').each(function() {
				if ($(this).attr('data-ont_name').toLowerCase().indexOf(text) != -1) {
					$(this).show();
				}
				else {
					$(this).hide();
				}
			});
		}, 200);
	});
	function errorCheck()
	{
		var errorTarget = $("#term_container .list_container #term_list #errorTrig");
		var empty = new Array();
		term_names = empty;
		if(errorTarget.val() > 0)
		{
			//alert(errorTarget.val());
			//updateTermString(empty, outerLimit, outerShowState, $("#terms_filtered"));
			//$("#terms_filtered").text("");
			$(".selected_terms").text("None");
			//$(".termExclude_container .more").remove();
			//$("#term_list").remove();
		}
	}

	//Instant search for term filter
	$("#term_filter_text").keyup(function(event) {
		var srchStr = $("#term_filter_text").val();
		var conf = $("#conf_num").val();
		$("#errorMsg").remove();
		if(srchStr.length >= 3) 
		{
			var data = 'searchString='+srchStr+'&conf='+conf;
			$(".filter_item_term").remove();
			$.ajax({
				type: "POST",
				url: "/stop/include/search.php",
				data: data,
				success: function(html){
					$("#term_list").append(html);
					filterTermChangeDisabled = true;
					$("#term_list .filter_checkbox_terms:not(:checked)").attr('checked', true);
					filterTermChangeDisabled = false;
					$("#term_list .filter_checkbox_terms:first").trigger('change');
					//errorCheck();
				}
			});

		}
		else
		{
			$(".filter_item_term").remove();
		}
		$("#errorTrig").remove();
	});

	//Select All Terms in filter
	$("#term_container .selection_container .select_all_terms").click(function() {
		filterTermChangeDisabled = true;
		$("#term_list .filter_checkbox_terms:not(:checked)").attr('checked', true);
		filterTermChangeDisabled = false;
		$("#term_list .filter_checkbox_terms:first").trigger('change');
	});

	//Select No Terms in filter
	$("#term_container .selection_container .select_none_terms").click(function() {
		filterTermChangeDisabled = true;
		$("#term_list .filter_checkbox_terms:checked").attr('checked', false);
		filterTermChangeDisabled = false;
		$("#term_list .filter_checkbox_terms:first").trigger('change');
	});

	//Select All Ontologies in filter
	$("#filter_dialog .selection_container .select_all").click(function() {
		filterChangeDisabled = true;
		$("#ontology_list .filter_checkbox:not(:checked)").attr('checked', true);
		filterChangeDisabled = false;
		$("#ontology_list .filter_checkbox:first").trigger('change');
	});

	//Select None in filter
	$("#filter_dialog .selection_container .select_none").click(function() {
		filterChangeDisabled = true;
		$("#ontology_list .filter_checkbox:checked").attr('checked', false);
		filterChangeDisabled = false;
		$("#ontology_list .filter_checkbox:first").trigger('change');
	});

	//Show/check items only in certain categories
	$("#filter_dialog #menu_container #category_menu").change(function() {
		var targetCategory = $(this).attr('value');			
		filterChangeDisabled = true;
		$("#ontology_list .filter_checkbox:checked").attr('checked', false);
		filterChangeDisabled = false;
		$("#ontology_list .filter_checkbox:first").trigger('change');
		names = [];
		ids = [];
		$("#ontology_list .filter_checkbox").each(function() {
			var crntOntName = $(this).val();
			var crntOntId = $(this).parent().attr('data-ont_id');
			var crntOntCategories = $(this).parent().attr('data-categories');
			var check = this;
			$(check).parent().show();
			$(check).parent().hide();
			if(crntOntCategories != "0")
			{
				var categories = crntOntCategories.split(',');
			}
			if(targetCategory == "all")
			{
				$(check).parent().show();
			}
			
			$(categories).each(function() {
				if(this == targetCategory)
				{
					if(crntOntName == $(check).val())
					{
						$(check).parent().show();
						$(check).attr('checked', true);
						names.push($.trim($(check).val()));
						ids.push($(check).parent().attr('data-ont_id'));
					}
				}

					
			});
			updateExcludeString(names, limit, showState, $("#filter_dialog .exclude_container .selected_items"));
		});
	});

	//Show/check items only in certain groups
	$("#filter_dialog #menu_container #group_menu").change(function() {
		var targetGroup = $(this).attr('value');			
		filterChangeDisabled = true;
		$("#ontology_list .filter_checkbox:checked").attr('checked', false);
		filterChangeDisabled = false;
		$("#ontology_list .filter_checkbox:first").trigger('change');
		names = [];
		ids = [];
		$("#ontology_list .filter_checkbox").each(function() {
			var crntOntName = $(this).val();
			var crntOntId = $(this).parent().attr('data-ont_id');
			var crntOntGroups = $(this).parent().attr('data-groups');
			var check = this;
			$(check).parent().show();
			$(check).parent().hide();
			if(crntOntGroups != "0")
			{
				var groups = crntOntGroups.split(',');
			}
			if(targetGroup == "all")
			{
				$(check).parent().show();
			}
			
			$(groups).each(function() {
				if(this == targetGroup)
				{
					if(crntOntName == $(check).val())
					{
						$(check).parent().show();
						$(check).attr('checked', true);
						names.push($.trim($(check).val()));
						ids.push($(check).parent().attr('data-ont_id'));
					}
				}
			});
			updateExcludeString(names, limit, showState, $("#filter_dialog .exclude_container .selected_items"));
		});
	});
	
	$("#ontology_list .filter_checkbox").change(function() {
		if (!filterChangeDisabled) {
			names = [];
			ids = [];
			
			$("#ontology_list .filter_checkbox:checked").each(function() {
				names.push($.trim($(this).val()));
				ids.push($(this).parent().attr('data-ont_id'));
			});
			updateExcludeString(names, limit, showState, $("#filter_dialog .exclude_container .selected_items"));
		}
	});

	$("#term_list").change(function() {
		if (!filterChangeDisabled) {
			term_names = [];
			term_ids = [];
			$("#term_list .filter_checkbox_terms").each(function() {
				if($(this).is(":checked"))
				{
					term_names.push($.trim($(this).val()));
					term_ids.push($(this).parent().attr('data-term_id'));	
				}
			});
			updateTermString(term_names, limit, showState2, $("#term_container .termExclude_container .selected_terms"));
		}
	});

	$("#filter_dialog .exclude_container .more").click(function() {
		showState = !showState;
		updateExcludeString(names, limit, showState, $("#filter_dialog .exclude_container .selected_items"));
	});

	$("#term_container .termExclude_container .more").click(function() {
		showState2 = !showState2;
		updateTermString(term_names, limit, showState2, $("#term_container .termExclude_container .selected_terms"));
	});

	//Trigger filter
	$("#filter_dialog .button_container .save").click(function() {
		$("#filter_dialog").dialog("close");
		updateTermString(term_names, outerLimit, outerShowState, $("#terms_filtered"));
		updateExcludeString(names, outerLimit, outerShowState, $("#ontologies_filtered"));
		$(document).trigger("ontology_filter", ids.join(','));
		$(document).trigger("term_filter", term_ids.join(','));
	});

	//Trigger Exporter
	$("#filter_dialog .button_container .export").click(function() {
		$("#filter_dialog").dialog("close");
		updateTermString(term_names, outerLimit, outerShowState, $("#terms_filtered"));
		updateExcludeString(names, outerLimit, outerShowState, $("#ontologies_filtered"));
		$(document).trigger("ontology_filter", ids.join(','));
		$(document).trigger("term_filter", term_ids.join(','));
		$(document).trigger("export_trigger", "true");
	});
	
	$("#filter_dialog .button_container .cancel").click(function() {
		$("#filter_dialog").dialog("close");
	});
	

	function updateTermString(names, limit, showState, target) {
		//alert('function updateTermString()');
		if (names.length > limit) {
			if (!showState) {
				var temp = [];
				for(var i = 0; i < limit; ++i) {
					temp.push(names[i]);
				}
				var text = temp.join(", ") + " and " + (names.length - limit) + " others";
				target.text(text);
				target.siblings(".more").text("show").show();
			}
			else {
				target.text(names.join(", "));
				target.siblings(".more").text("hide").show();
			}
		}
		else {
			if (names.length == 0) {
				target.text('None');
			}
			else {
				target.text(names.join(", "));
			}
			target.siblings(".more").hide();
		}
	}

	function updateExcludeString(names, limit, showState, target) {
		if (names.length > limit) {
			if (!showState) {
				var temp = [];
				for(var i = 0; i < limit; ++i) {
					temp.push(names[i]);
				}
				var text = temp.join(", ") + " and " + (names.length - limit) + " others";
				target.text(text);
				target.siblings(".more").text("show").show();
			}
			else {
				target.text(names.join(", "));
				target.siblings(".more").text("hide").show();
			}
		}
		else {
			if (names.length == 0) {
				target.text('None');
			}
			else {
				target.text(names.join(", "));
			}
			target.siblings(".more").hide();
		}
	}
});
