$(document).on('DOMNodeInserted', function(e) {
	if (e.target.id == 'success_added') {
   		var T = setTimeout(function() {
    		location.reload();
		}, 2000)
	}
});

$(document).ready(function(){
	$('.delete_repsite_page').on('click', function () {
        return confirm('Are you sure you want to delete this repsite page?');
    });


});

function getSubmitDivEditable(){
	document.getElementById("page_html_textarea").value = document.getElementById("editable_div").innerHTML;
}
