 $( document ).ready(function() {
 	$('.togglecat').click(function() {
	  	console.log("clicked the category link");
	    $(this).closest('.category-container').children('.subcat').toggle();
	});
});
