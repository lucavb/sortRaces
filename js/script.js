$(document).ready(function() {
	$.get("ajax.php?races=yes", function(data) {
		var obj = JSON.parse(data);
		jQuery.each(obj, function(i, val) {
			var string = "<a href='#' class='race-section list-group-item ui-state-default ";
			if (val.publish == 0) {
				string += 'list-group-item-danger\' data-publish=\'0\'';
			}
			else {
				string += "list-group-item-success\' data-publish=\'1\'";
			}
			string += " data-race=" + val.Rennen + " data-lauf='" + val.Lauf + "'>" + val.Rennen + " - " + val.Lauf + " - " + val.SollStartZeit + "</a>";
			
			$("#race_sort").append(string);
		});
		$('#race_sort').sortable();
	});

	$(document).on('click', "a.race-section", function(e) {
		e.preventDefault();
		if ($(this).attr("data-publish") == 1) {
			$(this).attr("data-publish", 0);
			$(this).removeClass("list-group-item-success");
			$(this).addClass("list-group-item-danger");
		}
		else {
			$(this).attr("data-publish", 1);
			$(this).removeClass("list-group-item-danger");
			$(this).addClass("list-group-item-success")
		}
	});

	$("#abc").click(function() {
		var arr = new Array();
		var listItems = $("#race_sort a");

		listItems.each(function(idx, li) {
		    var race = $(li).attr("data-race");
		    var lauf = $(li).attr("data-Lauf");
		    var publish = $(li).attr("data-publish");
		    arr.push(JSON.parse('{"race" : "' + race + '", "lauf" : "' + lauf + '", "publish" : "' + publish + '"}'));
		});
		var myJsonString = JSON.stringify(arr);
		$.post( "ajax.php", { "order": "yes", "list": myJsonString } );
	})
});