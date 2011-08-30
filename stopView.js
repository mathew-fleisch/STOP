$(document).ready(function() {
	var confId = getParameterByName("confID");
	var page = getParameterByName("page");
	var exclude = '';
	
	if (!page) {
		page = 1;
	}
	
	loadPage(page, exclude);
	
	$(document).bind('ontology_filter', function(event, excludeString) {
		exclude = excludeString;
		page = 1;
		loadPage(page, exclude);
	});
	
	$("#stop_view .name_value > a").live("click", function() {
		var title = escape($(this).attr('data-name'));
		ajax_showTooltip(window.event,'/stopOutput/ajax/getTerm.php?title=' + title + ' &confID=' + confId + '&exclude=' + exclude, this);
		return false;
	});
	
	$(".bar_view_pagination name_value a").live('click', function() {
		loadPage($(this).attr('data-page'), exclude);
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
	
	function loadPage(pageToRequest, exclude) {
		$("#stop_view").hide();
		$("#loading").show();
		
		if (!exclude) {
			exclude = '';
		}
		
		//We only use POST to make URL cleaner, does not change persistant data on server
		$.post("/stop/term-view/", {
			"confID": confId,
			"page": pageToRequest,
			"ajax": true,		//Required for our workaround, see barView.php
			"exclude": exclude
		}, function(response) {
			if (!response.error) {
				
				//Table cells
				$("#stop_view span#ont_term").remove();
				var template = $("#stop_view span.template:first");
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
					$("span.name_value a", clone).attr('data-name', item.term).text(text);
					$("span.name_value", clone).attr('id', 'ont_term');
					//$("name_value", clone).addClass((odd ? "odd":"even"));
					if (item.ench == "ENR") {
						$("span.name_value span", clone).addClass("Enr");
					}
					else if (item.ench == "DPL") {
						$("span.name_value span", clone).addClass("Dpl");
					}
					$("span.name_value a", clone).css('font-size', item.fontSize + "px");
					$("#stop_view").append(clone.removeClass('template'));
					odd = !odd;
				}
			}
			$("#loading").hide();
			$("#stop_view").show();
		});
	}
});
