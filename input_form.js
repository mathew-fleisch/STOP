$(document).ready(function(){
	$('#background_set').hide();
	$('#backLink').click(function() {
		$('#backLink').toggle();
		$('#removeBack').toggle();
		$('#background_set').toggle(400);
	});
	$('#removeBack').click(function() {
		$('#backLink').toggle();
		$('#removeBack').toggle();
		$('#textbox1').val("");
		$('#background_set').toggle(400);
	});
	$('#aboutLink').click(function() {
		$('#aboutText').toggle(400);
	});
	$('#addLink').click(function() {
		$('#addLink').toggle();
		$('#removeTitleLink').toggle();

		$('#addTitle').toggle(200);
	});
	$('#removeTitleLink').click(function() {
		$('#addLink').toggle();
		$('#removeTitleLink').toggle();
		$('#textbox_thin').val("");
		$('#addTitle').toggle(200);
	});
});

function jschange(o) {
	if(document.getElementById(o).style.display=='none') {
		document.getElementById(o).style.display='block';
	} else {
		document.getElementById(o).style.display='none';
	}
}



function geneProt() {
	var gene = "geneWhat";
	var prot = "protWhat";
	
	if(document.getElementById(gene).style.display=='none') {
		document.getElementById(gene).style.display='block';
	} else {
		document.getElementById(gene).style.display='none';
	}

	if(document.getElementById(prot).style.display=='none') {
		document.getElementById(prot).style.display='block';
	} else {
		document.getElementById(prot).style.display='none';
	}
}

function SetAllCheckBoxes(FormName, FieldName, CheckValue)
{

	if(!document.forms[FormName])
		return;
	var objCheckBoxes = document.forms[FormName].elements[FieldName];
	if(!objCheckBoxes)
		return;
	var countCheckBoxes = objCheckBoxes.length;
	if(!countCheckBoxes)
		objCheckBoxes.checked = CheckValue;
	else
		// set the check value for all check boxes
		for(var i = 0; i < countCheckBoxes; i++)
			objCheckBoxes[i].checked = CheckValue;
}
