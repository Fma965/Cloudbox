// Messy as fuck, needs to be rescripted

$( "#edit" ).click(function() {
		$( ".datesuffix" ).hide();
		$( ".fxamount" ).hide();
		$( "#currencytext" ).hide();
		$( "#currencyselect" ).show();
        $('.editablenum').each(function() {
           var itemData = $.trim($(this).text());
           var itemClass = $(this).attr('class');
		   var itemName = $(this).attr('name');
           var input = $('<input></input>');
           input.attr('step','0.01').addClass(itemClass).val(itemData).attr('name',itemName).attr('type','number').attr('class','form-control');
           $(this).replaceWith(input);
        });
		 $('.editable').each(function() {
           var itemData = $.trim($(this).text());
           var itemClass = $(this).attr('class');
		   var itemName = $(this).attr('name');
           var input = $('<input></input>');
           input.addClass(itemClass).val(itemData).attr('name',itemName).attr('type','text').attr('class','form-control');
           $(this).replaceWith(input);
        });
		$('.editabletextarea').each(function() {
           var itemData = $.trim($(this).text());
           var itemClass = $(this).attr('class');
		   var itemName = $(this).attr('name');
           var input = $('<textarea></textarea>');
           input.addClass(itemClass).val(itemData).attr('name',itemName).attr('class','form-control').css('height','90%').css('width','100%');
           $(this).replaceWith(input);
        });
		$('.editablecheckbox').each(function() {
           var itemData = $.trim($(this).text());
           var itemClass = $(this).attr('class');
		   var itemName = $(this).attr('name');
           var input = $('<input></input>');
           input.addClass(itemClass).val(itemData).attr('name',itemName).attr('type','checkbox').attr('class','form-control').attr('checked','true');
           $(this).replaceWith(input);
        });
		$('.plan-name').each(function() {
           var itemData = $(this).text();
           var itemClass = $(this).attr('class');
		   var itemID = $(this).attr('name');
		   var itemDataID = $(this).attr('data-id');
		   var itemName = $(this).attr('name');
           var input = $('#placeholder').clone();
           input.addClass(itemClass).val(itemDataID).attr('name',itemName).attr('id',itemID);
           $(this).replaceWith(input);
        });
		$( "#cancel" ).show();
		$( "#edit" ).hide();
		$( "#submit" ).show();
		$( "#status" ).hide();
		$( "#planbuttons").show();
}); 

$( "#cancel" ).click(function() {
		location.reload();
}); 

$("#add").click(function() {
	$( "#remove").show(); 
	var $name = $( ".plan select" ).last(); 
	var lastId = $name.prop('name');
	var newId  = lastId.replace(/(\d+)/, function(){return arguments[1]*1+1} );
	var name = $name.clone().prop('name', newId).prop('id',newId).prop('outerHTML');
 
	var $qty = $( ".plan input[type=number]" ).last();
	var lastId = $qty.prop('name');
	var newId  = lastId.replace(/(\d+)/, function(){return arguments[1]*1+1} );
	var qty = $qty.clone().prop('name', newId).prop('id',newId).prop('outerHTML');

	$( ".plan" ).last().after('<div class="plan"><div class="row"><div class="col-12 col-md-6">'+name+'\n'+qty+'</div></div></div>');
});

$("#remove").click(function() {
	$( ".plan" ).last().remove();
	if ($( ".plan" ).size() < 2) {
		$( "#remove").hide();
	}

});

$( "#toggleview" ).click(function() {	
		$( ".active-0" ).toggle();
		
}); 