$(document).ready(function() {
	var confId = getParameterByName("confID");
	var page = getParameterByName("page");
	var exclude = '';
	var termExclude = '';
	var exported = false;
	var loaded = false;
	
	if (!page) {
		page = 1;
	}
	

	//check to see if any ontologies have been filtered
	$(document).bind('ontology_filter', function(event, excludeString) {
		exclude = excludeString;
		page = 1;
		loaded = true;
		if(exclude.length)
		{
			loadPage(page, exclude, termExclude);
		}
	});

	//check to see if any terms have been filtered
	$(document).bind('term_filter', function(event, excludeString) {
		termExclude = excludeString;
		page = 1;
		loaded = true;
		if(termExclude.length)
		{
			loadPage(page, exclude, termExclude);
		}
	});
	
	//Button to trigger export of filtered results
	$(document).bind('export_trigger', function(event, exported) {

		page = 1;
		if(exported)
		{
			//alert("export_trigger="+exported);
			exportCSV(page, exclude, termExclude);
		}
	});

	if(!loaded)
	{
		loadPage(page, exclude, termExclude);
	}
	
	$("#bar_view .name_col > a").live("click", function() {
		var title = escape($(this).attr('data-name'));
		ajax_showTooltip(window.event,'/stopOutput/ajax/getTerm.php?title=' + title + ' &confID=' + confId + '&exclude=' + exclude, this);
		return false;
	});
	
	$(".bar_view_pagination table tr td a").live('click', function() {
		loadPage($(this).attr('data-page'), exclude, termExclude);
	});
	
	//From stackoverflow: http://stackoverflow.com/questions/901115/get-querystring-values-with-jquery
	function getParameterByName(name) {
	  name = name.replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]");
	  var regexS = "[\\?&]"+name+"=([^&#]*)";
	  var regex = new RegExp( regexS );
	  var results = regex.exec( window.location.href );
	  if( results == null )
	    return "";
	  else
	    return decodeURIComponent(results[1].replace(/\+/g, " "));
	}
	
	function loadPage(pageToRequest, exclude, termExclude) {
		$("#bar_view").hide();
		$("#loading").show();
		
		if (!exclude) {
			exclude = '';
		}

		if(!termExclude) {
			termExclude = '';
		}
		
		//We only use POST to make URL cleaner, does not change persistant data on server
		$.post("/stop/bar-view/", {
			"confID": confId,
			"page": pageToRequest,
			"ajax": true,		//Required for our workaround, see barView.php
			"exclude": exclude,
			"termExclude": termExclude
		}, function(response) {
			if (!response.error) {
				//Pagination
				//alert(response.notIn);
				var count = response.count;
				var last = Math.round(count/1000 - .5);
				page = response.page;
				var start = Math.max(1, page - 4);
				var end = Math.min(last, page + 4);
				var lastTd = $(".bar_view_pagination table tr td.last");
				
				$(".bar_view_pagination table tr td:not(.header, .first, .last)").remove();
				$(".bar_view_pagination table tr td.first, .bar_view_pagination table tr td.last").empty();
				
				for(var i = 0; i < 9; ++i) {
					var thisPage = start + i;
					if (page != thisPage) {
						lastTd.before($("<td>").append($("<a>").text(thisPage).attr('href', "javascript:;").attr('data-page', thisPage)));
					}
					else {
						lastTd.before($("<td>").text(page));
					}
					if (thisPage > last) {
						break;
					}
				}
				
				if (page != 0) {
					$(".bar_view_pagination table tr td.first").empty().append($("<a>").text("<<").attr('href', "javascript:;").attr('data-page', 1));
				}
				if (page != last) {
					lastTd.empty().append($("<a>").text(">>").attr('href', "javascript:;").attr('data-page', last));
				}
				
				//Table cells
				$("#bar_view tr:not(.template)").remove();
				var template = $("#bar_view tr.template:first");
				var odd = true;
				var track = 0;

				for(i in response.result) {
					track++;
					var clone = template.clone();
					var item = response.result[i];
					var text = item.term;
					if (item.termCount) {
						text += " (" + item.termCount + ")";
					}
					$("td.name_col a", clone).attr('data-name', item.term).text(text);
					$("td.name_col", clone).addClass((odd ? "odd":"even"));
					if (item.ench == "ENR") {
						$("td.pval_col", clone).addClass("Enr");
					}
					else if (item.ench == "DPL") {
						$("td.pval_col", clone).addClass("Dpl");
					}
					$("td.pval_col span", clone).css('width', item.width + "%").text(item.stringPval);
					$("#bar_view").append(clone.removeClass('template'));
					odd = !odd;
				}
			}
			$("#loading").hide();
			$("#bar_view").show();
		});

	}
	function exportCSV(pageToRequest, exclude, termExclude) {
		//alert("function exportCSV()");
		//Export to CSV using filtered results
		$.post("/stop/bar-view/", {
			"confID": confId,
			"page": pageToRequest,
			"exportMe": true,
			"ajax": true,		//Required for our workaround, see barView.php
			"exclude": exclude,
			"termExclude": termExclude
		}, function(response) {
			if (!response.error) {
				//Pagination
				//alert(response.notIn);
				var count = response.count;
				var dataHolder = "Ontology(id)\tid\tterm\tStudy Count/Total\tBackground Count/total\tUncorrected P-Value\tCorrected P-Value\n";
				var track = 0;

				for(i in response.result)
				{
					track++;
					var item = response.result[i];
					if(item.ont_name){
						var t_OntName = jQuery.trim(item.ont_name);
					}else{
						var t_OntName = "";
					}
					if(item.ont_id){
						var t_OntID = jQuery.trim(item.ont_id);
					}else{
						var t_OntID = "";
					}
					if(item.term_id){
						var t_termID = jQuery.trim(item.term_id);
					}else{
						var t_termID = "";
					}
					if(item.term){
						var t_term = jQuery.trim(item.term);
					}else{
						var t_term = "";
					}
					if(item.study_count){
						var t_StudyCount = jQuery.trim(item.study_count);
					}else{
						var t_StudyCount = "";
					}
					if(item.study_total){
						var t_StudyTotal = jQuery.trim(item.study_total);
					}else{
						var t_StudyTotal = "";
					}
					if(item.bg_count){
						var t_BGCount = jQuery.trim(item.bg_count);
					}else{
						var t_BGCount = "";
					}
					if(item.bg_total){
						var t_BGTotal = jQuery.trim(item.bg_total);
					}else{
						var t_BGTotal = "";
					}
					if(item.uncorrectedPval){
						var t_unpVal = jQuery.trim(item.uncorrectedPval);
					}else{
						var t_unpVal = "";
					}
					if(item.correctedPval){
						var t_pVal = jQuery.trim(item.correctedPval);
					}else{
						var t_pVal = "";
					}
					var temp = t_OntName+"("+t_OntID+")\t"+t_termID+"\t"+t_term+"\t"+t_StudyCount+"/"+t_StudyTotal+"\t"+t_BGCount+"/"+t_BGTotal+"\t"+t_unpVal+"\t"+t_pVal+"\n";
					dataHolder += temp;
				}

				$("#exporter").append('<form id="exportform" action="/stop/include/export.php" method="post" target="_blank"><input type="hidden" id="exportdata" name="exportdata" /></form>');
				$("#exportdata").val(dataHolder);
				$("#exportform").submit().remove();

				//alert("results count: "+count);
			}
		});
	}
});
